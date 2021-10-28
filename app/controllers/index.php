<?php
/**
 * The root controller, this is always called if desired one is missing.
 */



// attempts to find a view (.twig file).
$view = $this->locate_view(null);



// if the view is going back to index.twig, and there are uri parts, do a 404.
if (stripos($view, 'index.twig') !== false && $this->request->uri_parts_count > 0) {
    // you could opt to show your own 404 page instead.
	header('HTTP/1.0 404 File Not Found');
	exit;
}



// present the view.
$this->present_view($view, ['title' => 'Home Page']);