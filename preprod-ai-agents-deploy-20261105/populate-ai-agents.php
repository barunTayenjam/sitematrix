<?php
/**
 * Populate the "The AI Agents We Have Built" block on the AI Agent page.
 *
 * Usage (from the WordPress root, on preprod):
 *   wp eval-file populate-ai-agents.php
 *
 * Safe to re-run: update_post_meta is idempotent.
 */

$slug = 'ai-agent-development-services';

// Find the page by slug, regardless of parent path.
$pages = get_posts(array(
    'post_type'      => 'page',
    'post_status'    => 'any',
    'name'           => $slug,
    'posts_per_page' => 1,
    'fields'         => 'ids',
));

if (empty($pages)) {
    WP_CLI::error("Page with slug '{$slug}' not found.");
}

$page_id = (int) $pages[0];

$fields = array(
    'feature_description' => 'Our AI agent development services are grounded in real enterprise deployments across sales, engineering, HR, and competitive intelligence.',
    'feature_title_1'     => 'Sales Enablement Agent',
    'feature_desc_1'      => 'An intelligent sales agent integrated with internal knowledge systems to help teams navigate complex service portfolios through natural language queries. The agent delivers instant, pitch-ready responses, improving onboarding efficiency and strengthening cross-sell and upsell conversations.',
    'feature_title_2'     => 'BRIX - Intelligent Code Generation Platform',
    'feature_desc_2'      => 'A multi-agent AI system that converts Figma designs, images, or prompts into production-ready code with built-in accessibility and performance optimization. The platform accelerates design-to-code workflows, enhances code consistency, and improves collaboration between design and engineering teams.',
    'feature_title_3'     => 'Paid Time Off (PTO) Automation Agent',
    'feature_desc_3'      => 'A multi-agent automation system that validates leave policies, processes requests across HR platforms, reconciles calendar entries, and flags discrepancies automatically. The solution reduces manual intervention, improves policy compliance, and streamlines end-to-end PTO management.',
    'feature_title_4'     => 'Enterprise Research Agent',
    'feature_desc_4'      => 'An AI-powered multi-agent platform designed to gather, classify, and synthesize competitive intelligence into structured, executive-ready reports. The system significantly reduces research cycles while providing continuous, data-backed strategic insights.',
    'feature_bg_color'    => '#f0f9ff',
);

foreach ($fields as $key => $value) {
    update_post_meta($page_id, $key, $value);
}

WP_CLI::success("Updated " . count($fields) . " fields on '{$slug}' (post ID {$page_id}).");
