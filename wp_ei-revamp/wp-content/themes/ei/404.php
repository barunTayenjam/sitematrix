<?php
/**
 * 404 — 10K Agency.
 *
 * @package Ei
 */

get_header(); ?>

<section class="sheet page-sheet">
	<div class="container page-inner">
		<span class="mono-micro" style="display:block;margin-bottom:16px;">Error 404 — Signal lost</span>
		<h1 style="font-family:'Anton',sans-serif;font-size:clamp(4rem,10vw,10rem);line-height:0.9;letter-spacing:-0.04em;color:var(--ink);text-transform:uppercase;">
			404<span class="vermilion">.</span>
		</h1>
		<p class="serif-accent" style="margin-top:16px;">lost signal.</p>
		<p class="body-l" style="margin-top:32px;">The specimen you requested is outside our index.</p>
		<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-top:24px;">&larr; Return to index</a>
	</div>
</section>

<?php get_footer(); ?>
