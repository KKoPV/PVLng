<?php
/**
 *
 * @author      Knut Kohl <github@knutkohl.de>
 * @copyright   2012-2013 Knut Kohl
 * @license     GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version     $Id$
 */

// ---------------------------------------------------------------------------
// Route not found, redirect to index instead
// ---------------------------------------------------------------------------
$app->notFound(function() use ($app) {
	$app->redirect('/');
});

// ---------------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------------
$app->map('/login', function() use ($app) {
	$app->process('Admin', 'Login');
})->via('GET', 'POST');

$app->any('/logout', function() use ($app) {
	$app->process('Admin', 'Logout');
});

$app->map('/adminpass', function() use ($app) {
	$app->process('Admin', 'AdminPassword');
})->via('GET', 'POST');

// ---------------------------------------------------------------------------
// Index
// ---------------------------------------------------------------------------
// User check is done inside controller, only save and delete needs login!
$app->map('/', function() use ($app) {
	$app->process('Index', 'Index');
})->via('GET', 'POST');

$app->map('/index', function() use ($app) {
	$app->process('Index', 'Index');
})->via('GET', 'POST');

$app->get('/index(/:view(/:date))', function( $view='', $date='' ) use ($app) {
	$app->process('Index', 'Index', array('view' => $view, 'date' => $date));
});

$app->get('/chart/:view(/:date)', function( $view, $date='' ) use ($app) {
	$app->process('Index', 'Index', array('view' => $view, 'date' => $date));
});

// ---------------------------------------------------------------------------
// Dashboard
// ---------------------------------------------------------------------------
$app->map('/dashboard', $checkAuth, function() use ($app) {
	$app->process('Dashboard', 'Index');
})->via('GET', 'POST');

$app->get('/ed', $checkAuth, function() use ($app) {
	$app->process('Dashboard', 'IndexEmbedded');
});

// ---------------------------------------------------------------------------
// Overview
// ---------------------------------------------------------------------------
$app->get('/overview', $checkAuth, function() use ($app) {
	$app->process('Overview', 'Index');
});

$app->post('/overview/:action', $checkAuth, function( $action ) use ($app) {
	$app->process('Overview', $action);
});

// ---------------------------------------------------------------------------
// Channel
// ---------------------------------------------------------------------------
$app->get('/channel', $checkAuth, function() use ($app) {
	$app->process('Channel', 'Index');
});

$app->map('/channel/add(/:clone)', $checkAuth, function( $clone=0 ) use ($app) {
	$app->process('Channel', 'Add', array('clone' => $clone));
})->via('GET', 'POST');

$app->get('/channel/edit/:id', $checkAuth, function( $id ) use ($app) {
	$app->process('Channel', 'Edit', array('id' => $id));
});

$app->post('/channel/edit', $checkAuth, function() use ($app) {
	$app->process('Channel', 'Edit');
});

$app->post('/channel/delete', $checkAuth, function() use ($app) {
	$app->process('Channel', 'Delete');
});

// ---------------------------------------------------------------------------
// Info
// ---------------------------------------------------------------------------
$app->get('/info', $checkAuth, function() use ($app) {
	$app->process('Info', 'Index');
});

$app->post('/info', $checkAuth, function() use ($app) {
	$app->process('Info', 'Index');
});

// ---------------------------------------------------------------------------
// Description
// ---------------------------------------------------------------------------
$app->get('/description', function() use ($app) {
	$app->process('Description', 'Index');
});

// ---------------------------------------------------------------------------
// Mobile
// ---------------------------------------------------------------------------
$app->get('/m', function() use ($app) {
	$app->process('Mobile', 'Index');
});
