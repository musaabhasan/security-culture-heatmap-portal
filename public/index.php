<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $assetPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $assetFile = realpath(__DIR__ . $assetPath);

    if ($assetFile !== false && str_starts_with($assetFile, __DIR__) && is_file($assetFile)) {
        return false;
    }
}

use Portal\Repository\PortalRepository;
use Portal\Security\Csrf;
use Portal\Security\SecurityHeaders;
use Portal\Service\ScoringService;
use Portal\Support\Database;
use Portal\Support\Env;
use Portal\Support\Json;
use Portal\Support\View;

require __DIR__ . '/../src/bootstrap.php';

SecurityHeaders::apply();
Csrf::start();

$config = Json::decode((string) file_get_contents(__DIR__ . '/../config/portal.json'), []);
$repository = new PortalRepository(Database::tryConnection());
$scoring = new ScoringService();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($path === '/health') {
    jsonResponse(['status' => 'ok', 'service' => $config['slug'] ?? 'portal']);
}

if ($path === '/api/summary') {
    jsonResponse([
        'portal' => $config['title'],
        'book_alignment' => $config['book'],
        'summary' => $repository->summary(),
        'dimension_averages' => $repository->dimensionAverages(),
    ]);
}

if ($path === '/assessment' && $method === 'POST') {
    handleAssessmentPost($config, $repository, $scoring);
}

if ($path === '/assessment') {
    sendPage($config, 'New Assessment', renderAssessmentForm($config, $repository));
}

if ($path === '/roadmap') {
    sendPage($config, 'Roadmap', renderRoadmap($config, $repository));
}

sendPage($config, 'Dashboard', renderDashboard($config, $repository, $scoring));

function handleAssessmentPost(array $config, PortalRepository $repository, ScoringService $scoring): void
{
    if (!Csrf::valid($_POST['_csrf_token'] ?? null)) {
        sendPage($config, 'Session expired', '<section class="panel"><h1>Session expired</h1><p>Please go back and try again.</p></section>', 419);
    }

    if (!$repository->connected()) {
        sendPage($config, 'Database unavailable', '<section class="panel"><h1>Database unavailable</h1><p>Connect MySQL and load the migrations before saving assessments.</p></section>', 503);
    }

    $subjectName = trim((string) ($_POST['subject_name'] ?? ''));
    $subjectType = trim((string) ($_POST['subject_type'] ?? 'Team'));
    $notes = trim((string) ($_POST['notes'] ?? ''));
    if ($subjectName === '' || strlen($subjectName) > 160) {
        sendPage($config, 'Validation error', '<section class="panel"><h1>Validation error</h1><p>A subject name under 160 characters is required.</p></section>', 422);
    }

    $scores = [];
    foreach ($config['dimensions'] ?? [] as $dimension) {
        $key = (string) $dimension['key'];
        $scores[$key] = max(1, min(5, (float) ($_POST['score'][$key] ?? 1)));
    }

    $weightedScore = $scoring->weightedScore($config['dimensions'] ?? [], $scores);
    $uuid = $repository->createAssessment($subjectName, $subjectType, $scores, $notes, $weightedScore);
    header('Location: /?saved=' . rawurlencode($uuid), true, 302);
    exit;
}

function sendPage(array $config, string $title, string $body, int $status = 200): void
{
    http_response_code($status);
    echo layout($config, $title, $body);
    exit;
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo Json::encode($payload);
    exit;
}

function layout(array $config, string $title, string $body): string
{
    $appTitle = View::e((string) ($config['title'] ?? 'Portal'));
    $pageTitle = View::e($title);
    $short = View::e((string) ($config['short_name'] ?? 'PORTAL'));
    $accent = View::e((string) ($config['accent'] ?? '#0f766e'));

    return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$pageTitle} | {$appTitle}</title>
  <style>:root { --primary: {$accent}; }</style>
  <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="/"><span class="brand-mark">{$short}</span><span>{$appTitle}</span></a>
    <nav>
      <a href="/">Dashboard</a>
      <a href="/assessment">Assessment</a>
      <a href="/roadmap">Roadmap</a>
      <a href="/api/summary">API</a>
    </nav>
  </header>
  <main class="page-shell">{$body}</main>
</body>
</html>
HTML;
}

function renderDashboard(array $config, PortalRepository $repository, ScoringService $scoring): string
{
    $summary = $repository->summary();
    $averages = $repository->dimensionAverages();
    $initiatives = $repository->initiatives() ?: ($config['initiatives'] ?? []);
    $recent = $repository->recentAssessments();
    $score = $summary['average_score'] ?? null;
    $band = $score === null ? 'No assessments yet' : $scoring->maturityBand((float) $score);
    $scoreLabel = $score === null ? 'Ready' : number_format((float) $score, 1) . '%';
    $dbStatus = $summary['connected'] ? 'MySQL connected' : 'MySQL not connected';
    $assessmentCount = (string) ($summary['assessment_count'] ?? 0);
    $initiativeCount = (string) ($summary['initiative_count'] ?? 0);
    $latestAssessment = View::e((string) ($summary['latest_assessment_at'] ?? 'No records yet'));
    $saved = isset($_GET['saved']) ? '<div class="notice">Assessment saved successfully.</div>' : '';
    $dimensionCards = renderDimensionCards($config, $averages);
    $initiativeCards = renderInitiatives($initiatives);
    $recentRows = renderRecentAssessments($recent);
    $book = $config['book'];
    $portalTitle = View::e((string) $config['title']);
    $bookTitle = View::e((string) $book['title']);
    $bookUrl = View::e((string) $book['url']);
    $tagline = View::e((string) $config['tagline']);
    $alignment = View::e((string) $book['alignment']);

    return <<<HTML
{$saved}
<section class="hero panel">
  <div>
    <p class="eyebrow">Book-aligned security culture portal</p>
    <h1>{$portalTitle}</h1>
    <p>{$tagline}</p>
    <div class="hero-actions">
      <a class="button-link" href="/assessment">Run assessment</a>
      <a class="secondary-link" href="{$bookUrl}" target="_blank" rel="noopener">Book reference</a>
    </div>
  </div>
  <aside class="book-card">
    <span>Reference</span>
    <strong>{$bookTitle}</strong>
    <small>{$alignment}</small>
  </aside>
</section>
<section class="metric-grid">
  <article><span>Maturity score</span><strong>{$scoreLabel}</strong><small>{$band}</small></article>
  <article><span>Assessments</span><strong>{$assessmentCount}</strong><small>{$latestAssessment}</small></article>
  <article><span>Initiatives</span><strong>{$initiativeCount}</strong><small>Seeded improvement portfolio</small></article>
  <article><span>Data layer</span><strong>{$dbStatus}</strong><small>PHP 8.x + MySQL 8.0</small></article>
</section>
<section class="section-head"><h2>Assessment Dimensions</h2><a href="/assessment">Capture evidence</a></section>
<section class="dimension-grid">{$dimensionCards}</section>
<section class="split-layout">
  <div>
    <section class="section-head"><h2>Improvement Portfolio</h2><a href="/roadmap">View roadmap</a></section>
    <div class="stack">{$initiativeCards}</div>
  </div>
  <aside class="panel">
    <h2>Recent Assessments</h2>
    {$recentRows}
  </aside>
</section>
HTML;
}

function renderDimensionCards(array $config, array $averages): string
{
    $html = '';
    foreach ($config['dimensions'] ?? [] as $dimension) {
        $key = (string) $dimension['key'];
        $label = View::e((string) $dimension['label']);
        $description = View::e((string) $dimension['description']);
        $average = isset($averages[$key]) ? number_format(((float) $averages[$key] / 5) * 100, 0) . '%' : 'Not scored';
        $html .= "<article class=\"panel dimension-card\"><span>{$average}</span><h3>{$label}</h3><p>{$description}</p></article>";
    }

    return $html;
}

function renderInitiatives(array $initiatives): string
{
    $html = '';
    foreach ($initiatives as $initiative) {
        $title = View::e((string) ($initiative['title'] ?? 'Initiative'));
        $owner = View::e((string) ($initiative['owner'] ?? 'Owner pending'));
        $status = View::e((string) ($initiative['status'] ?? 'planned'));
        $priority = View::e((string) ($initiative['priority'] ?? 'medium'));
        $impact = View::e((string) ($initiative['impact_area'] ?? 'culture'));
        $html .= "<article class=\"initiative\"><div><strong>{$title}</strong><span>{$owner}</span></div><div><em>{$impact}</em><span class=\"badge\">{$priority}</span><span class=\"badge soft\">{$status}</span></div></article>";
    }

    return $html;
}

function renderRecentAssessments(array $recent): string
{
    if ($recent === []) {
        return '<p class="muted">No assessments have been saved yet. Use the assessment form to create the first evidence record.</p>';
    }

    $html = '<div class="recent-list">';
    foreach ($recent as $row) {
        $subject = View::e((string) $row['subject_name']);
        $score = number_format((float) $row['weighted_score'], 1) . '%';
        $band = View::e((string) $row['maturity_band']);
        $html .= "<div><strong>{$subject}</strong><span>{$score} · {$band}</span></div>";
    }

    return $html . '</div>';
}

function renderAssessmentForm(array $config, PortalRepository $repository): string
{
    $disabled = $repository->connected() ? '' : '<div class="notice warning">MySQL is not connected. The form is visible, but saving requires the database service.</div>';
    $csrfField = Csrf::field();
    $fields = '';
    foreach ($config['dimensions'] ?? [] as $dimension) {
        $key = View::e((string) $dimension['key']);
        $label = View::e((string) $dimension['label']);
        $description = View::e((string) $dimension['description']);
        $fields .= <<<HTML
<label class="score-control">
  <span><strong>{$label}</strong><small>{$description}</small></span>
  <select name="score[{$key}]">
    <option value="1">1 - Initial</option>
    <option value="2">2 - Basic</option>
    <option value="3" selected>3 - Developing</option>
    <option value="4">4 - Advanced</option>
    <option value="5">5 - Leading</option>
  </select>
</label>
HTML;
    }

    return <<<HTML
{$disabled}
<section class="panel form-panel">
  <p class="eyebrow">Assessment intake</p>
  <h1>Capture a new maturity evidence record</h1>
  <form method="post" action="/assessment">
    {$csrfField}
    <div class="form-grid">
      <label>Subject name <input required name="subject_name" maxlength="160" placeholder="Example: Finance Department"></label>
      <label>Subject type <select name="subject_type"><option>Team</option><option>Department</option><option>Program</option><option>Use Case</option><option>Scenario</option></select></label>
    </div>
    <div class="score-grid">{$fields}</div>
    <label>Evidence notes <textarea name="notes" rows="4" placeholder="Summarize the evidence behind this assessment."></textarea></label>
    <button type="submit">Save assessment</button>
  </form>
</section>
HTML;
}

function renderRoadmap(array $config, PortalRepository $repository): string
{
    $workflow = '';
    foreach ($config['workflows'] ?? [] as $index => $item) {
        $number = $index + 1;
        $workflow .= '<article><span>' . $number . '</span><p>' . View::e((string) $item) . '</p></article>';
    }

    $evidence = '';
    foreach ($config['evidence_examples'] ?? [] as $item) {
        $evidence .= '<li>' . View::e((string) $item) . '</li>';
    }

    return <<<HTML
<section class="panel">
  <p class="eyebrow">Implementation roadmap</p>
  <h1>From assessment to measurable security culture improvement</h1>
  <p class="muted">Use this roadmap to connect assessment findings with owners, initiatives, evidence, and periodic review.</p>
</section>
<section class="roadmap">{$workflow}</section>
<section class="panel">
  <h2>Evidence Standard</h2>
  <ul class="evidence-list">{$evidence}</ul>
</section>
HTML;
}
