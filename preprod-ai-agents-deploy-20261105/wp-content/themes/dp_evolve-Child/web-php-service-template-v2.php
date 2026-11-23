<?php
/* Template Name: Web service Master Template - V2
   Template Post Type: page */

get_header('gzInner');
get_sidebar('fullbanner');
?>

<link rel="stylesheet" href="<?php echo esc_url(get_stylesheet_directory_uri() . '/css/web-php-service--template-v2.css'); ?>" />

<?php
/* ===== Section: Our Partner / Customers ===== */
$customers_bg_color  = get_field('our_customer_bg_color');
$customers_heading   = get_field('our_customer_title');
$customers_shortcode = get_field('our_customer_shortcode');
?>

<?php if (! empty($customers_heading)) : ?>
    <section class="our-partner-light bg-image color-black text-center"
        <?php if (! empty($customers_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($customers_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container text-center row justify-content-center mx-auto">
            <h2 class="our-partner-heading col-md-12">
                <?php echo wp_kses_post($customers_heading); ?>
            </h2>

            <?php if (! empty($customers_shortcode)) : ?>
                <div class="col-md-12">
                    <div class="partners-carousel-container">
                        <?php echo do_shortcode($customers_shortcode); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>


<?php
/* ===== Section: Collaborate / Java Development Solutions ===== */
$collab_bg_color = get_field('collabrate_service_bg_color');
$collab_heading  = get_field('collabrate_title');
$collab_para     = get_field('collabarator_para');
$collab_image    = get_field('collabrate_right-img');

// Generate unique section ID
$collab_unique_id = 'java_dev_solutions_' . uniqid();
?>

<?php if (!empty($collab_heading)) : ?>
    <section id="<?php echo esc_attr($collab_unique_id); ?>"
        class="java-development-solutions"
        <?php if (!empty($collab_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($collab_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">

                <div class="col-lg-6 col-md-12">
                    <?php if (!empty($collab_image)) : ?>
                        <div class="java-development-image">
                            <img src="<?php echo esc_url($collab_image['url']); ?>"
                                alt="<?php echo esc_attr($collab_heading); ?>" />
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-6 col-md-12 digital-growth-content-img">
                    <div class="java-development-solutions-content">
                        <p class="java-development-solutions-heading">
                            <?php echo wp_kses_post($collab_heading); ?>
                        </p>

                        <?php if (!empty($collab_para)) : ?>
                            <p class="java-development-para">
                                <?php echo wp_kses_post($collab_para); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>
<?php
/* ===== Section: Step by Step Process ===== */
$problem_bg_color = get_field('problem_bg_color');
$problem_heading  = get_field('problem_heading_');
$problem_left_img = get_field('problem_image');
$process_line_img = get_field('problem_icon_line');
?>

<?php if (! empty($problem_heading)) : ?>
    <section class="step-by-step-process"
        <?php if (! empty($problem_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($problem_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">

                <div class="col-lg-5 col-md-12">
                    <div class="step-process-heading">
                        <h2><?php echo wp_kses_post($problem_heading); ?></h2>

                        <?php if (! empty($problem_left_img)) : ?>
                            <div class="step-by-step-img">
                                <img src="<?php echo esc_url($problem_left_img['url']); ?>" alt="<?php echo esc_attr($problem_heading); ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-7 col-md-12">
                    <div class="steps-sequence-wrapper">

                        <?php for ($i = 1; $i <= 8; $i++) :
                            $icon_img = get_field('problem_icon_' . $i);
                            $title    = get_field('problem_title_' . $i);
                            $desc     = get_field('problem_descption_' . $i);
                            // Skip this iteration if there's no content
                            if (empty($title) && empty($desc) && empty($icon_img)) continue;
                        ?>
                            <div class="steps-process-info">

                                <div class="steps-info-img">
                                    <?php if ( isset($icon_img["url"]) && !empty($icon_img["url"]) ): ?>
                                        <img src="<?php echo esc_url($icon_img['url']); ?>" alt="<?php echo esc_attr($title); ?>">
                                    <?php endif; ?>

                                    <?php if ( isset($process_line_img["url"]) && !empty($process_line_img["url"]) ): ?>
                                        <img class="process-line-img" src="<?php echo esc_url($process_line_img['url']); ?>" alt="process line">
                                    <?php endif; ?>
                                </div>

                                <div class="community-services">
                                    <?php if (! empty($title)) : ?>
                                        <h4><?php echo esc_html($title); ?></h4>
                                    <?php endif; ?>

                                    <?php if (! empty($desc)) : ?>
                                        <p><?php echo wp_kses_post($desc); ?></p>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endfor; ?>

                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>


<?php
$why_bg_color = get_field('why_bg_color');
$why_heading  = get_field('why_heading');
?>

<?php if ($why_heading) : ?>
    <section class="why-choose-section"
        <?php if (!empty($why_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($why_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

                <!-- Section Heading -->
                <h2 class="why-heading text-center">
                    <?php echo esc_html($why_heading); ?>
                </h2>

                <?php
                // Pre-collect cards
                $why_cards = [];
                for ($i = 1; $i <= 10; $i++) :
                    $image = get_field("card_img_$i");
                    $title = get_field("card_title_$i");
                    $desc  = get_field("card_desc_$i");
                    if ($image || $title || $desc) :
                        $why_cards[] = compact('image', 'title', 'desc');
                    endif;
                endfor;
                $use_carousel = count($why_cards) > 4;
                ?>

                <!-- Cards Wrapper -->
                <div class="why-cards-wrapper <?php echo $use_carousel ? 'why-cards-carousel-wrap' : 'why-cards-grid'; ?>">

                    <?php if ($use_carousel) : ?>
                    <div class="owl-carousel owl-theme why-cards-carousel">
                    <?php endif; ?>

                        <?php foreach ($why_cards as $card) :
                            extract($card);
                        ?>
                            <div class="why-card">
                                <div class="flip-box">
                                    <div class="flip-inner">
                                        <div class="front">
                                            <?php if (!empty($image)) : ?>
                                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($title); ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="back">
                                            <?php if (!empty($desc)) : ?>
                                                <p><?php echo esc_html($desc); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($title)) : ?>
                                    <h4 class="card-title"><?php echo esc_html($title); ?></h4>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    <?php if ($use_carousel) : ?>
                    </div>
                    <?php endif; ?>

                </div>

        </div>
    </section>
<?php endif; ?>


<?php
$scalable_bg_color = get_field('scalable_bg_color');
$heading           = get_field('scalable_heading');
if(!empty($heading)):
?>

<section class="business-benefits-microsoft"
    <?php if (!empty($scalable_bg_color)) : ?>
    style="background-color: <?php echo esc_attr($scalable_bg_color); ?>;"
    <?php endif; ?>>
    <div class="container">

        <!-- Heading -->
        <?php if (!empty($heading)) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="business-benefits-microsoft-content text-center common-heading-color">
                        <h2><?php echo wp_kses_post($heading); ?></h2>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col">
                <div class="owl-carousel owl-theme from-our-blog-carousel">

                    <?php for ($i = 1; $i <= 10; $i++) :

                        $icon  = get_field("scalable_icon_$i");
                        $title = get_field("scalable_title_$i");
                        $desc  = get_field("scalable_des_$i");
                        $color = get_field("scalable_color_$i");

                        if (empty($title)) continue;
                    ?>
                        <div class="item">
                            <div class="business-benefit-card"
                                style="background-color: <?php echo esc_attr($color); ?>;">

                                <?php if (!empty($icon)) : ?>
                                    <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($title); ?>" />
                                <?php endif; ?>

                                <h6><?php echo esc_html($title); ?></h6>
                                <p><?php echo esc_html($desc); ?></p>

                            </div>
                        </div>
                    <?php endfor; ?>

                </div>
            </div>
        </div>

    </div>
</section>
<?php endif; ?>

<?php
$tab1 = get_field('tab_title_1');
$tab2 = get_field('tab_title_2');
$tab3 = get_field('tab_title_3');
$tab4 = get_field('tab_title_4');
$tab5 = get_field('tab_title_5');
$tab6 = get_field('tab_title_6');

$tab_section_bg_color = get_field('tab_bg_color');
$tab_heading = get_field('tab_heading');
$tab_description= get_field('tab_description');
?>

<?php if (!empty($tab_heading)) : ?>
    <section
        class="tech-stack-section py-5"
        <?php if (!empty($tab_section_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($tab_section_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="section-title text-center">
                <?php echo esc_html($tab_heading); ?>
            </h2>
             <p class="section-tec-para text-center">
                <?php echo esc_html($tab_description); ?>
            </p>
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="techStackTabs" role="tablist">

                <?php if ($tab1) : ?>
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#core">
                            <?php echo esc_html($tab1); ?>
                        </button>
                    </li>
                <?php endif; ?>

                <?php if ($tab2) : ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#frontend">
                            <?php echo esc_html($tab2); ?>
                        </button>
                    </li>
                <?php endif; ?>

                <?php if ($tab3) : ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#apis">
                            <?php echo esc_html($tab3); ?>
                        </button>
                    </li>
                <?php endif; ?>

                <?php if ($tab4) : ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#devops">
                            <?php echo esc_html($tab4); ?>
                        </button>
                    </li>
                <?php endif; ?>

                <?php if ($tab5) : ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#performance">
                            <?php echo esc_html($tab5); ?>
                        </button>
                    </li>
                <?php endif; ?>

                <?php if ($tab6) : ?>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#database">
                            <?php echo esc_html($tab6); ?>
                        </button>
                    </li>
                <?php endif; ?>

            </ul>
            <div class="tab-content pt-4"> <!-- -------------------- CORE TAB -------------------- -->
                <div class="tab-pane fade show active" id="core">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("core_icon_$i");
                                                $text = get_field("core_text_$i");
                                                if (!$icon && !$text) {
                                                    continue;
                                                }
                                            ?> <div class="tech-card"> <?php if (
                                                    $icon
                                                ): ?> <img src="<?php echo esc_url(
                                                        $icon["url"]
                                                    ); ?>" alt="" class="tech-logo"> <?php endif; ?> <?php if (
                                                        $text
                                                    ): ?> <p><?php echo esc_html($text); ?></p> <?php endif; ?> </div> <?php
                                                                endfor; ?> </div>
                </div> <!-- -------------------- FRONT-END TAB -------------------- -->
                <div class="tab-pane fade" id="frontend">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("fe_icon_$i");
                                                $text = get_field("fe_text_$i");
                                                if (!$icon && !$text) {
                                                    continue;
                                                }
                                            ?> 
                                            <div class="tech-card"> 
                                            <?php if ( isset($icon["url"]) && !empty($icon["url"])): ?> 
                                                <img src="<?php echo esc_url($icon["url"]); ?>" alt="" class="tech-logo"> 
                                            <?php endif; ?> 
                                            <?php if (isset($text)): ?> 
                                                <p><?php echo esc_html($text); ?></p> 
                                            <?php endif; ?> 
                                            </div> 
                                            <?php endfor; ?> 
                                            </div>
                </div> <!-- -------------------- API TAB -------------------- -->
                <div class="tab-pane fade" id="apis">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("api_ixon_$i");
                                                $text = get_field("api_text_$i");
                                                if ( !isset($icon["url"]) && empty($icon["url"]) ){
                                                    continue;
                                                }
                                            ?> <div class="tech-card"> <?php if (
                                                    $icon
                                                ): ?> <img src="<?php echo esc_url(
                                                        $icon["url"]
                                                    ); ?>" alt="" class="tech-logo"> <?php endif; ?> <?php if (
                                                        $text
                                                    ): ?> <p><?php echo esc_html($text); ?></p> <?php endif; ?> </div> <?php
                                                                endfor; ?> </div>
                </div> <!-- -------------------- DEVOPS TAB -------------------- -->
                <div class="tab-pane fade" id="devops">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("devepos_icon_$i");
                                                $text = get_field("devepos_text_$i");
                                                if ( !isset($icon["url"]) && empty($icon["url"]) ){
                                                    continue;
                                                }
                                            ?> <div class="tech-card"> <?php if (
                                                    $icon
                                                ): ?> <img src="<?php echo esc_url(
                                                        $icon["url"]
                                                    ); ?>" alt="" class="tech-logo"> <?php endif; ?> <?php if (
                                                        $text
                                                    ): ?> <p><?php echo esc_html($text); ?></p> <?php endif; ?> </div> <?php
                                                                endfor; ?> </div>
                </div> <!-- -------------------- PERFORMANCE TAB -------------------- -->
                <div class="tab-pane fade" id="performance">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("integration_icon_$i");
                                                $text = get_field("integration_text_$i");
                                                if ( !isset($icon["url"]) && empty($icon["url"]) ){
                                                    continue;
                                                }
                                            ?> <div class="tech-card"> <?php if (
                                                    $icon
                                                ): ?> <img src="<?php echo esc_url(
                                                        $icon["url"]
                                                    ); ?>" alt="" class="tech-logo"> <?php endif; ?> <?php if (
                                                        $text
                                                    ): ?> <p><?php echo esc_html($text); ?></p> <?php endif; ?> </div> <?php
                                                                endfor; ?> </div>
                </div> <!-- -------------------- DATABASE TAB -------------------- -->
                <div class="tab-pane fade" id="database">
                    <div class="tech-grid"> <?php for (
                                                $i = 1;
                                                $i <= 9;
                                                $i++
                                            ):

                                                $icon = get_field("cd_icon_$i");
                                                $text = get_field("cd_text_$i");
                                                if ( !isset($icon["url"]) && empty($icon["url"]) ){
                                                    continue;
                                                }
                                            ?> <div class="tech-card"> <?php if (
                                                    $icon
                                                ): ?> <img src="<?php echo esc_url(
                                                        $icon["url"]
                                                    ); ?>" alt="" class="tech-logo"> <?php endif; ?> <?php if (
                                                        $text
                                                    ): ?> <p><?php echo esc_html($text); ?></p> <?php endif; ?> </div> <?php
                                                                endfor; ?> </div>
                </div>

            </div>
    </section>
<?php endif; ?>




<?php
/* ===== Integrate Your Online (Next Section) ===== */
$next_bg_color   = get_field('next_bg_color');
$next_heading    = get_field('next_heading');
$next_para       = get_field('next_paragrapgh');
$next_cta_title  = get_field('next_cta_title');
?>

<?php if (! empty($next_heading)) : ?>
    <section
        class="integrate-your-online"
        <?php if (! empty($next_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($next_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="text-center">
                <?php echo wp_kses_post($next_heading); ?>
            </h2>

            <?php if (! empty($next_para)) : ?>
                <p class="text-center">
                    <?php echo wp_kses_post($next_para); ?>
                </p>
            <?php endif; ?>

            <?php if (! empty($next_cta_title)) : ?>
                <div class="git-btn d-flex justify-content-center">
                    <a class="gz-btn integrate-btn" href="javascript:void(0)" onclick="customscroll('.get-in-touch')">
                        <?php echo wp_kses_post($next_cta_title); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>


<?php
/* ===== MS Dynamics Video Section ===== */
$video_bg_color     = get_field('video_bg_color');
$video_heading      = get_field('video_heading');
$video_description  = get_field('video_desc');
$video_url          = get_field('video_url');
$video_fallback_img = get_field('video_img');

$video_btn_text     = get_field('video_button_text');
$video_btn_url      = get_field('video_button_url');

$consult_btn_text   = get_field('consultant_url_text');
$consult_btn_url    = get_field('consutant_url');
?>

<?php if (! empty($video_heading)) : ?>
    <section
        class="ms-dynamics-video"
        <?php if (! empty($video_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($video_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <div class="row">

                <!-- LEFT CONTENT -->
                <div class="col-lg-6 col-md-12">
                    <div class="end-to-end-ms-headings">

                        <h3><?php echo wp_kses_post($video_heading); ?></h3>

                        <?php if (! empty($video_description)) : ?>
                            <p class="custom-para">
                                <?php echo wp_kses_post($video_description); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (! empty($video_btn_text) && ! empty($video_btn_url)) : ?>
                            <div class="consult-btn-wrap">
                                <a href="<?php echo esc_url($video_btn_url); ?>" target="_blank" class="consult-btn">
                                    <?php echo esc_html($video_btn_text); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- RIGHT VIDEO / IMAGE -->
                <div class="col-lg-6 col-md-12 text-algn">

                    <?php if (! empty($video_url)) : ?>
                        <iframe
                            src="<?php echo esc_url($video_url); ?>"
                            title="Video player"
                            width="560"
                            height="315"
                            frameborder="0"
                            allowfullscreen>
                        </iframe>

                    <?php elseif (! empty($video_fallback_img)) : ?>
                        <img
                            src="<?php echo esc_url($video_fallback_img['url']); ?>"
                            alt="<?php echo esc_attr($video_fallback_img['alt']); ?>">
                    <?php endif; ?>

                    <?php if (! empty($video_btn_text) && ! empty($video_btn_url)) : ?>
                        <!-- <div class="audit text-center">
                            <a href="<?php //echo esc_url($video_btn_url); ?>" class="cta-button">
                                <?php //echo esc_html($video_btn_text); ?>
                            </a>
                        </div> -->
                    <?php endif; ?>

                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php
/* ===== Section: Deliver / Step-by-Step Process ===== */
$deliver_bg_color = get_field('deliver_bg_color');
$deliver_heading  = get_field('deliver_heading');
$deliver_section_description  = get_field('deliver_section_description');
?>

<?php if (! empty($deliver_heading)) : ?>
    <section class="our-comprehensive"
        <?php if (! empty($deliver_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($deliver_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="text-center">
                <?php echo wp_kses_post($deliver_heading); ?>
            </h2>

            <?php if(!empty($deliver_section_description)): ?>
                <p><?php echo $deliver_section_description; ?></p>
            <?php endif; ?>

            <div class="row our-comprehensive-row mt-4">

                <?php
                for ($i = 1; $i <= 10; $i++) :

                    $deliver_icon_img = get_field('deliver_icon_img_' . $i);
                    $deliver_title    = get_field('deliver_title_' . $i);
                    $deliver_desc     = get_field('deliver_desc_' . $i);

                    if (empty($deliver_icon_img) && empty($deliver_title) && empty($deliver_desc)) {
                        continue;
                    }
                ?>
                    <div class="col-lg-6 col-md-12">
                        <div class="our-comprehensive-col">

                            <?php if (! empty($deliver_icon_img)) : ?>
                                <div class="our-comprehensive-icon">
                                    <img
                                        decoding="async"
                                        src="<?php echo esc_url($deliver_icon_img['url']); ?>"
                                        alt="<?php echo esc_attr($deliver_icon_img['alt']); ?>" />
                                </div>
                            <?php endif; ?>

                            <div class="our-comprehensive-content">

                                <?php if (! empty($deliver_title)) : ?>
                                    <h4><?php echo wp_kses_post($deliver_title); ?></h4>
                                <?php endif; ?>

                                <?php if (! empty($deliver_desc)) : ?>
                                    <p><?php echo wp_kses_post($deliver_desc); ?></p>
                                <?php endif; ?>

                            </div>

                        </div>
                    </div>
                <?php endfor; ?>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php
$development_processs_section = get_field('development_processs_section');
$development_processs_section_heading = $development_processs_section['heading'] ?? '';
$development_processs_section_description = $development_processs_section['description'] ?? '';

if( !empty($development_processs_section_heading) ): 

    // Background settings
    $background_settings = $development_processs_section['background'];
    $background_color = $background_settings['background_color'];
    $background = "";

    if( $background_settings['background_image'] && !empty($background_settings['background_image']['url']) ):
        $background = "background: url(" . $background_settings['background_image']['url'] . "); background-repeat: no-repeat; background-size: cover";
    else:
        if( !empty($background_color) ):
            $background = "background-color: $background_color";
        endif;
    endif;

?>

<section class="excellence section section--dark section-spacer-tb" style="<?php echo esc_attr($background); ?>">
    <div class="excellence__container container">
        <div class="section__header">
            <h2 class="excellence__heading section__heading"><?php echo $development_processs_section_heading; ?></h2>
            <p class="process-section__subtitle"><?php echo $development_processs_section_description; ?></p>
        </div>
        <div class="excellence__grid">

            <?php
            // Access the repeater inside the group
            if ( !empty($development_processs_section['process_cards']) ) :
                foreach ( $development_processs_section['process_cards'] as $item ) : ?>
                    <div class="excellence__card" style="border: 1px solid <?php echo $item['color'] ?? '#0A3EA7'; ?>">
                        <?php 
                        $icon = $item['icon'] ?? [];
                        if( isset($icon['icon']) && !empty($icon['url']) ): 
                        ?>
                            <div class="card__icon">
                                <img src="<?php echo $icon['url']; ?>" alt="<?php echo $item['title']; ?>">
                            </div>
                        <?php endif; ?>
                        <h3 class="excellence__card-title"><?php echo $item['title']; ?></h3>
                        <p class="excellence__card-description"><?php echo $item['description']; ?></p>
                    </div>
                <?php endforeach;
            endif;
            ?>

        </div>
    </div>
</section>
<?php endif; ?>

<?php
/* ===== Customer Testimonials Slider Section ===== */
$customers_testimonial_bg_color  = get_field('customer_testimonial_bg_color');
$customers_testimonial_heading   = get_field('customer_testimonial_heading');
$customers_testimonial_shortcode = get_field('customer_tesimonial_shortcode');
?>

<?php if (! empty($customers_testimonial_heading)) : ?>
    <section
        class="gz-spacer-80 carousel-bullets testimonial-carousel-bullets bg-image custom-bg-dark color-white text-center"
        <?php if (! empty($customers_testimonial_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($customers_testimonial_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container text-center">
            <h2 class="testimonial-heading"><?php echo wp_kses_post($customers_testimonial_heading); ?></h2>

            <?php if (! empty($customers_testimonial_shortcode)) : ?>
                <div class="mx-auto my-auto position-relative">
                    <?php echo do_shortcode($customers_testimonial_shortcode); ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<?php
$solution_bg_color   = get_field('solution_bg_color');
$solution_heading    = get_field('solution_heading');
$solution_left_image = get_field('solution_left_image');
$solution_shortcode  = get_field('solution_shortcode');

// Unique ID for first FAQ section
$faq_unique_id_1 = 'faq_section_one_' . uniqid();
?>

<?php if (!empty($solution_heading)) : ?>
    <section class="surf-the-ai tab-content drupal-faq-sec"
        <?php if (!empty($solution_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($solution_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <div class="row">
                <div class="col">
                    <div class="surf-the-ai-content text-center">
                        <h2><?php echo wp_kses_post($solution_heading); ?></h2>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-6">
                    <?php if (!empty($solution_left_image)) : ?>
                        <img class="img-fluid" src="<?php echo esc_url($solution_left_image['url']); ?>"
                            alt="<?php echo esc_attr($solution_heading); ?>">
                    <?php endif; ?>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="accordion" id="<?php echo esc_attr($faq_unique_id_1); ?>">
                        <?php
                        // Replace all IDs inside the shortcode output with unique ID
                        echo do_shortcode(
                            str_replace(
                                ['phpFaqAccordion', 'accordion'], // old IDs inside HTML
                                $faq_unique_id_1,                 // new unique ID
                                $solution_shortcode
                            )
                        );
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php
/* ===== Feature Section ===== */
$feature_bg_color = get_field('feature_bg_color');
$feature_heading  = get_field('feature_heading');
$feature_description = get_field('feature_description');
?>

<?php if (! empty($feature_heading)) : ?>
    <section class="why-integrate"
        <?php if (! empty($feature_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($feature_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">

                <h2 class="heading">
                    <?php echo esc_html($feature_heading); ?>
                </h2>

                <?php if (! empty($feature_description)) : ?>
                    <p class="why-integrate-subtitle text-center">
                        <?php echo esc_html($feature_description); ?>
                    </p>
                <?php endif; ?>

                <!-- 2x2 card grid -->
                <div class="why-integrate-grid">

                    <?php
                    for ($i = 1; $i <= 7; $i++) :

                        $img   = get_field("feature_img_$i");
                        $title = get_field("feature_title_$i");
                        $desc  = get_field("feature_desc_$i");

                        if (empty($img) && empty($title) && empty($desc)) {
                            continue;
                        }
                    ?>
                        <div class="why-integrate-slide">

                            <div class="head-icon">

                                <?php if (! empty($img)) : ?>
                                    <div class="icon">
                                        <img
                                            src="<?php echo esc_url($img['url']); ?>"
                                            alt="<?php echo esc_attr($img['alt'] ?? $title ?? 'feature-icon'); ?>">
                                    </div>
                                <?php endif; ?>

                                <?php if (! empty($title)) : ?>
                                    <h5><?php echo wp_kses_post($title); ?></h5>
                                <?php endif; ?>

                            </div>

                            <?php if (! empty($desc)) : ?>
                                <p><?php echo esc_html($desc); ?></p>
                            <?php endif; ?>

                        </div>
                    <?php endfor; ?>

                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
/* ===== Integrate Your Online (Next Section) ===== */
$drupal_bg_color = get_field('drupal_bg_color');
if(!empty(get_field('drupal_heading'))):
?>

<section class="marketing-centralizes"
    <?php if (! empty($drupal_bg_color)) : ?>
    style="background-color: <?php echo esc_attr($drupal_bg_color); ?>;"
    <?php endif; ?>>
    <div class="container">

        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="marketing-centralizes-content common-heading-color">
                    <h2><?php echo wp_kses_post(get_field('drupal_heading')); ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="owl-carousel owl-theme from-our-blog-carousel">

                    <?php
                    // Loop through 6 static ACF field sets
                    for ($i = 1; $i <= 10; $i++) :

                        $img   = get_field("drupal_img_$i");
                        $title = get_field("drupal_title_$i");
                        $desc  = get_field("drupal_desc_$i");

                        // Skip empty fields
                        if (!$img && !$title && !$desc) continue;
                    ?>

                        <div class="item">
                            <div class="leverage-ms-cards marketing-centralizes-card">

                                <?php if (!empty($img)) : ?>
                                    <img
                                        src="<?php echo esc_url($img['url']); ?>"
                                        alt="<?php echo esc_attr($title); ?>" />
                                <?php endif; ?>

                                <div class="leverage-ms-card-content marketing-centralizes-card-content">

                                    <?php if (!empty($title)) : ?>
                                        <h6><?php echo esc_html($title); ?></h6>
                                    <?php endif; ?>

                                    <?php if (!empty($desc)) : ?>
                                        <p><?php echo $desc; ?></p>
                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>

                    <?php endfor; ?>

                </div>
            </div>
        </div>

    </div>
</section>
<?php endif; ?>

<?php
/**
 * Development Services Technology Stack Section
 *
 * ACF Fields:
 * enable_development_services_technology_stack → true_false
 * development_services_technology_stack_content → group
 *   ├── background_image      → image (array)
 *   ├── background_color      → color_picker
 *   ├── heading               → text
 *   ├── sub_heading           → wysiwyg
 *   ├── slide_hover_bg_color  → color_picker
 *   └── slides                → repeater
 *       ├── slide_icon        → image (array)
 *       ├── slide_title       → text
 *       └── slide_description → wysiwyg
 */

$dst_enabled  = get_field( 'enable_development_services_technology_stack' );
$dst_section  = get_field( 'development_services_technology_stack_content' );
$dst_title    = $dst_section['heading']             ?? '';
$dst_sub      = $dst_section['sub_heading']         ?? '';
$dst_bg_color = $dst_section['background_color']    ?? '';
$dst_bg_img   = $dst_section['background_image']    ?? null;
$dst_hover_bg = $dst_section['slide_hover_bg_color'] ?? '#1483C6';
$dst_slides   = $dst_section['slides']              ?? [];

$dst_style = '';
if ( ! empty( $dst_bg_img['url'] ) ) {
    $dst_style .= 'background-image: url(' . esc_url( $dst_bg_img['url'] ) . '); background-size: cover; background-position: center;';
}
if ( ! empty( $dst_bg_color ) ) {
    $dst_style .= 'background-color: ' . esc_attr( $dst_bg_color ) . ';';
}
?>

<?php if ( $dst_enabled && ! empty( $dst_slides ) ) : ?>

<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/slick/slick.min.css">
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/slick/slick-theme.min.css">

<section class="dst-section" <?php if ( $dst_style ) echo 'style="' . $dst_style . '"'; ?>>
    <div class="container">

        <?php if ( $dst_title || $dst_sub ) : ?>
        <div class="dst-header text-center">
            <?php if ( $dst_title ) : ?>
                <h2 class="dst-title"><?php echo wp_kses_post( $dst_title ); ?></h2>
            <?php endif; ?>
            <?php if ( $dst_sub ) : ?>
                <div class="dst-subtitle"><?php echo wp_kses_post( $dst_sub ); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="dst-grid">
            <?php foreach ( $dst_slides as $slide ) :
                $icon    = $slide['slide_icon']        ?? null;
                $s_title = $slide['slide_title']       ?? '';
                $s_desc  = $slide['slide_description'] ?? '';
            ?>
            <div class="dst-card" data-hover-bg="<?php echo esc_attr( $dst_hover_bg ); ?>">
                <?php if ( ! empty( $icon['url'] ) ) : ?>
                <div class="dst-card__icon">
                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                         alt="<?php echo esc_attr( $icon['alt'] ?? $s_title ); ?>"
                         loading="lazy">
                </div>
                <?php endif; ?>
                <?php if ( $s_title ) : ?>
                <h3 class="dst-card__title"><?php echo esc_html( $s_title ); ?></h3>
                <?php endif; ?>
                <?php if ( $s_desc ) : ?>
                <div class="dst-card__desc"><?php echo wp_kses_post( $s_desc ); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/slick/slick.min.js"></script>
<script>
jQuery(document).ready(function ($) {
    var $grid = $('.dst-grid');
    if (!$grid.length || typeof $.fn.slick === 'undefined') return;

    $grid.slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        infinite: false,
        speed: 400,
        prevArrow: '<button type="button" class="slick-prev custom-arrow"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.00085 10.0723L6.00819 9.05786L2.73614 5.75566L14.2883 5.75566L14.2883 4.31679L2.73614 4.31679L6.01533 1.01458L5.00086 0.00018038L-9.39766e-05 5.03622L5.00085 10.0723Z" fill="#0070F0"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next custom-arrow"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.28748 0L8.28014 1.0144L11.5522 4.31661H0V5.75548H11.5522L8.273 9.05768L9.28748 10.0721L14.2884 5.03604L9.28748 0Z" fill="#0070F0"/></svg></button>',
        responsive: [
            { breakpoint: 1024, settings: { slidesToShow: 2 } },
            { breakpoint: 768,  settings: { slidesToShow: 1 } }
        ]
    });
});
</script>

<?php endif; ?>



<?php
/* ===== YouTube Testimonial Section ===== */
$testimonial_heading      = get_field('testimonial_heaidng');
$testimonial_color        = get_field('testimonial_bg_color');
$testimonial_sub_heading  = get_field('testimonial_sub_heading');
$testimonial_paragraph    = get_field('testimonial_paragraph');
$testimonial_link         = get_field('testimonial_link');
?>

<?php if (! empty($testimonial_heading)) : ?>
    <section class="client-testimonials-section"
        <?php if (! empty($testimonial_color)) : ?>
        style="background-color: <?php echo esc_attr($testimonial_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2><?php echo wp_kses_post($testimonial_heading); ?></h2>

            <?php if (! empty($testimonial_sub_heading)) : ?>
                <h5><?php echo wp_kses_post($testimonial_sub_heading); ?></h5>
            <?php endif; ?>

            <?php if (! empty($testimonial_paragraph)) : ?>
                <p><?php echo wp_kses_post($testimonial_paragraph); ?></p>
            <?php endif; ?>

            <?php if (! empty($testimonial_link)) : ?>
                <div class="client-video-yt">
                    <iframe width="720" height="480"
                        src="<?php echo esc_url($testimonial_link); ?>"
                        title="YouTube video player"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen>
                    </iframe>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>




<?php
/* ===== Website Development Section ===== */
$development_bg_color   = get_field('develpoment_bg_color');
$development_heading    = get_field('development_heading');
$development_paragraph  = get_field('development_paragraph');
$development_image      = get_field('development_image');
?>

<?php if (! empty($development_heading)) : ?>
    <section class="Website-Process"
        <?php if (! empty($development_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($development_bg_color); ?>;"
        <?php endif; ?>>
        <div class="Website-Process-inner container">

            <h2 class="Website-Process-heading text-center">
                <?php echo wp_kses_post($development_heading); ?>
            </h2>

            <?php if (! empty($development_paragraph)) : ?>
                <p class="Website-Process-sub-heading text-center">
                    <?php echo wp_kses_post($development_paragraph); ?>
                </p>
            <?php endif; ?>

            <?php if (! empty($development_image)) : ?>
                <div class="Website-Process-content">
                    <div class="desktop-view">
                        <img
                            src="<?php echo esc_url($development_image['url']); ?>"
                            alt="<?php echo esc_attr($development_heading); ?>"
                            class="development-image" />
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>


<?php
/* ===== Steps Section ===== */
$step_bg_color  = get_field('steps_bg_color');
$step_heading   = get_field('steps_heading');
$steps_process_paragraph   = get_field('steps_process_paragraph');
$steps_image    = get_field('steps_image');
?>

<?php if (! empty($step_heading)) : ?>
    <section class="steps-Process"
        <?php if (! empty($step_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($step_bg_color); ?>;"
        <?php endif; ?>>
        <div class="steps-Process-inner container">

            <h2 class="steps-Process-heading text-center">
                <?php echo wp_kses_post($step_heading); ?>
            </h2>

            <?php if(!empty($steps_process_paragraph)): ?>
                <p><?php echo $steps_process_paragraph; ?></p>
            <?php endif; ?>

            <?php if (! empty($steps_image)) : ?>
                <div class="steps-Process-content">
                    <div class="steps-desktop-view">
                        <img
                            src="<?php echo esc_url($steps_image['url']); ?>"
                            alt="<?php echo esc_attr($step_heading); ?>"
                            class="steps-image" />
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>



<?php
/* ===== Platform Section ===== */
$platform_bg_color  = get_field('platform_bg_color');
$platform_heading   = get_field('platform_heading');
$platform_image     = get_field('platform_image');
?>

<?php if (! empty($platform_heading)) : ?>
    <section class="platform-section"
        <?php if (! empty($platform_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($platform_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">
                <h2 class="platform-heading"><?php echo esc_html($platform_heading); ?></h2>

                <?php if (! empty($platform_image)) : ?>
                    <div class="platform-image text-center">
                        <img src="<?php echo esc_url($platform_image['url']); ?>" alt="<?php echo esc_attr($platform_heading); ?>" class="platform-image" />
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php
/* ===== CTA Section 2 ===== */
$cta_group    = get_field('cta_section_2');
$cta_2_heading = $cta_group['heading'] ?? '';
?>

<?php 

if (!empty($cta_2_heading)) : 

    /*Background settings*/
    $cta_2_background = $cta_group['background'];
    $cta_2_background_color = $cta_group['background_color'];
    $background = "";

    $cta_2_description = $cta_group['description'] ?? '';
    $cta_2_button = $cta_group['button'] ?? [];


    if( isset($cta_2_background['background_image']) && !empty($cta_2_background['background_image']['url']) ):
        $background = "background: url(" . $cta_2_background['background_image']['url'] . "); background-repeat: no-repeat; background-size: cover";
    else:
        if( !empty($cta_2_background_color) ):
            $background = "background-color: $cta_2_background_color";
        endif;
    endif;

?>
    <section class="integrate-your-online cta-section-2" style="<?php echo esc_attr($background); ?>">
        <div class="container">

            <h2 class="text-center">
                <?php echo wp_kses_post($cta_2_heading); ?>
            </h2>

            <?php if (! empty($next_para)) : ?>
                <p class="text-center">
                    <?php echo wp_kses_post($cta_2_description); ?>
                </p>
            <?php endif; ?>

            <?php if ( isset($cta_2_button['url']) && !empty($cta_2_button['url']) ) : ?>
                <?php
                $btn_url = $cta_2_button['url'];
                $is_anchor = strpos($btn_url, '#') === 0;
                ?>
                <div class="git-btn d-flex justify-content-center">
                    <?php if ($is_anchor) : ?>
                        <a class="gz-btn integrate-btn" href="javascript:void(0)" onclick="customscroll('<?php echo esc_js($btn_url); ?>')">
                            <?php echo wp_kses_post($cta_2_button['title']); ?>
                        </a>
                    <?php else : ?>
                        <a class="gz-btn integrate-btn" href="<?php echo esc_url($btn_url); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo wp_kses_post($cta_2_button['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>


<?php
/* ===== Section: Our Customers ===== */
$our_customer_bg_color     = get_field('our_customer_bg_color');
$our_customer_heading      = get_field('our_customer_heading');
$our_customer_triangle_img = get_field('our_customer_triangle_image');
?>

<?php if (! empty($our_customer_heading)) : ?>
    <section class="our-customers-sec2"
        <?php if (! empty($our_customer_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($our_customer_bg_color); ?>;"
        <?php endif; ?>>

        <?php if (! empty($our_customer_triangle_img)) : ?>
            <img
                decoding="async"
                src="<?php echo esc_url($our_customer_triangle_img['url']); ?>"
                alt="<?php echo esc_attr($our_customer_triangle_img['alt'] ?? 'Our Customers Graphic'); ?>"
                class="Intersection-blue">
        <?php endif; ?>

        <div class="our-customers-sec2-inner container">

            <h2 class="our-customers-sec2-heading">
                <?php echo wp_kses_post($our_customer_heading); ?>
            </h2>

            <div class="our-customers-sec2-logos row">

                <?php
                for ($i = 1; $i <= 6; $i++) :
                    $our_customer_logo = get_field('our_customer_logo_' . $i);

                    if (empty($our_customer_logo)) {
                        continue;
                    }
                ?>
                    <div class="col-md-2 col-6 text-center">
                        <img
                            decoding="async"
                            src="<?php echo esc_url($our_customer_logo['url']); ?>"
                            alt="<?php echo esc_attr($our_customer_logo['alt'] ?? 'Customer Logo'); ?>">
                    </div>
                <?php endfor; ?>

            </div>
        </div>

    </section>
<?php endif; ?>


<?php
$consult_bg_color   = get_field('consultant_bg_color');
$consult_heading    = get_field('consultant_heading');
$consult_subheading = get_field('consultant_subheading');
$consult_btn_text   = get_field('consultant_url_text');
$consult_btn_url    = get_field('consutant_url');
?>

<?php if (! empty($consult_heading)) : ?>
    <section class="consult-section"
        <?php if (! empty($consult_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($consult_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="consult-title text-center">
                <?php echo wp_kses_post($consult_heading); ?>
            </h2>

            <?php if (! empty($consult_subheading)) : ?>
                <p class="consult-subtitle text-center">
                    <?php echo wp_kses_post($consult_subheading); ?>
                </p>
            <?php endif; ?>

            <div class="consult-features">
                <?php for ($i = 1; $i <= 4; $i++) :
                    $icon = get_field('consultant_icon_' . $i);
                    $text = get_field('consultant_text_' . $i);

                    if (empty($icon) && empty($text)) {
                        continue;
                    }
                ?>
                    <div class="consult-item">

                        <?php if (! empty($icon['url'])) : ?>
                            <img
                                src="<?php echo esc_url($icon['url']); ?>"
                                alt="<?php echo esc_attr($icon['alt'] ?? 'feature-icon'); ?>">
                        <?php endif; ?>

                        <?php if (! empty($text)) : ?>
                            <span><?php echo esc_html($text); ?></span>
                        <?php endif; ?>

                    </div>
                <?php endfor; ?>
            </div>

            <?php if (! empty($consult_btn_text) && ! empty($consult_btn_url)) : ?>
                <div class="consult-btn-wrap text-center">
                    <a href="<?php echo esc_url($consult_btn_url); ?>" class="consult-btn">
                        <?php echo esc_html($consult_btn_text); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>




<?php
$section_bg_color   = get_field('section_bg_color');
$section_heading    = get_field('section_heading');
$section_subheading = get_field('section_sub_heading');
?>

<?php if (! empty($section_heading)) : ?>
    <section class="global-market-pos-section"
        <?php if (! empty($section_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($section_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <!-- Section Heading -->
            <h2 class="section-title text-center">
                <?php echo $section_heading; ?>
            </h2>

            <!-- Sub Heading -->
            <?php if (! empty($section_subheading)) : ?>
                <p class="section-subtitle text-center">
                    <?php echo $section_subheading; ?>
                </p>
            <?php endif; ?>

            <div class="row justify-content-center mt-4">

                <?php
                for ($i = 1; $i <= 2; $i++) :

                    $icon  = get_field("card_{$i}_icon");
                    $cad_title = get_field("card_{$i}_tite"); // FIXED
                    $desc  = get_field("card_description_{$i}");

                    if (empty($icon) && empty($cad_title) && empty($desc)) {
                        continue;
                    }
                ?>
                    <div class="col-md-6 mb-4">
                        <div class="gm-card">

                            <div class="gm-card-top">

                                <?php if (!empty($icon)) : ?>
                                    <div class="gm-card-icon">
                                        <img
                                            src="<?php echo esc_url($icon['url']); ?>"
                                            alt="<?php echo esc_attr($icon['alt'] ?? $title ?? 'card-icon'); ?>">
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($cad_title)) : ?>
                                    <h4 class="gm-card-title">
                                        <?php echo esc_html($cad_title); ?>
                                    </h4>
                                <?php endif; ?>

                            </div>

                            <div class="gm-card-bottom">
                                <?php if (! empty($desc)) : ?>
                                    <p class="gm-card-desc">
                                        <?php echo $desc; ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                <?php endfor; ?>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php
/* ===== Section: Deliver / Step-by-Step Process ===== */
$expertise_bg_color = get_field('expertise_bg_color');
$expertise_heading  = get_field('expertise_heading');
$next_cta_title  = get_field('next_cta_title');
?>

<?php if (!empty($expertise_heading)) : ?>
    <section class="grazitti-expertise"
        <?php if (!empty($expertise_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($expertise_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="what-makes-exterise"><?php echo wp_kses_post($expertise_heading); ?></h2>

            <div class="row">
                <?php
                for ($i = 1; $i <= 4; $i++) :

                    $expertise_icon_img = get_field('expertise_icon_img_' . $i);
                    $expertise_title    = get_field('expertise_title_' . $i);

                    if (empty($expertise_icon_img) && empty($expertise_title)) continue;
                ?>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="microsoft-gold grazitti-exper text-center">

                            <?php if (!empty($expertise_icon_img)) : ?>
                                <div class="grazitti-expertise-icon">
                                    <img decoding="async" src="<?php echo esc_url($expertise_icon_img['url']); ?>" alt="<?php echo esc_attr($expertise_icon_img['alt'] ?? $expertise_title ?? 'expertise-icon'); ?>">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($expertise_title)) : ?>
                                <p class="what-makes-para"><?php echo wp_kses_post($expertise_title); ?></p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endfor; ?>
                <?php if (! empty($next_cta_title)) : ?>
                    <div class="git-btn d-flex justify-content-center">
                        <a class="gz-btn integrate-btn" href="javascript:void(0)" onclick="customscroll('.get-in-touch')">
                            <?php echo wp_kses_post($next_cta_title); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>
<?php endif; ?>

<?php
/* ===== Section: Deliver / Step-by-Step Process ===== */
$optimize_bg_color       = get_field('optimze_bg_color');
$optimize_heading        = get_field('optimze_heading');
$optimize_paragraph      = get_field('optimze_paragrapgh');
$optimize_sub_paragraph  = get_field('optimze_sub_paragrpgh');

/* Correct field from screenshot */
$optimize_cta_url        = get_field('optimze_cta');
$optimize_cta_title      = get_field('optimze_cta_title_');
?>

<?php if (!empty($optimize_heading)) : ?>
    <section class="grazitti-expertise grazitti-optimize"
        <?php if (!empty($optimize_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($optimize_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <h2 class="what-makes"><?php echo wp_kses_post($optimize_heading); ?></h2>

            <?php if (!empty($optimize_paragraph)) : ?>
                <p class="para-description"><?php echo wp_kses_post($optimize_paragraph); ?></p>
            <?php endif; ?>

            <?php if (!empty($optimize_sub_paragraph)) : ?>
                <p class="para"><?php echo wp_kses_post($optimize_sub_paragraph); ?></p>
            <?php endif; ?>

            <div class="row">
                <?php for ($i = 1; $i <= 4; $i++) :

                    $optimize_icon_img = get_field('optimze_icon_img_' . $i);
                    $optimize_title    = get_field('optimze_title_' . $i);

                    if (empty($optimize_icon_img) && empty($optimize_title)) continue;
                ?>
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="microsoft-gold grazitti-exper text-center">

                            <?php if (!empty($optimize_icon_img)) : ?>
                                <div class="grazitti-expertise-icon">
                                    <img decoding="async" src="<?php echo esc_url($optimize_icon_img['url']); ?>" alt="">
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($optimize_title)) : ?>
                                <p class="what-makes-para"><?php echo wp_kses_post($optimize_title); ?></p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <?php if (!empty($optimize_cta_url) && !empty($optimize_cta_title)) : ?>
                <div class="audit text-center">
                    <a href="<?php echo esc_url($optimize_cta_url); ?>" class="cta-button">
                        <?php echo esc_html($optimize_cta_title); ?>
                    </a>
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>

<?php
$unique_id = 'ems_slider_' . wp_rand(1000, 9999);

$egaged_bg_color            = get_field('egaged_bg_color');
$egaged_service_heading     = get_field('egaged_service_heading'); // FIXED
$egaged_service_sub_heading = get_field('egaged_service_sub_heading');
?>

<?php if (! empty($egaged_service_heading)) : ?>
    <section class="engagement-models-section ems-module-<?php echo esc_attr($unique_id); ?>"
        <?php if (! empty($egaged_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($egaged_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <!-- Section Heading -->
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="ems-heading">
                        <?php echo wp_kses_post($egaged_service_heading); ?>
                    </h2>
                </div>
            </div>

            <!-- Sub Heading -->
            <?php if (! empty($egaged_service_sub_heading)) : ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="ems-subheading">
                            <?php echo wp_kses_post($egaged_service_sub_heading); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CAROUSEL -->
            <div id="<?php echo esc_attr($unique_id); ?>"
                class="owl-carousel owl-theme engagement-models-carousel">

                <?php
                for ($i = 1; $i <= 6; $i++) :

                    $image       = get_field("egaged_service_img_$i");
                    $hover_image = get_field("egaged_hover_img_$i");
                    $title       = get_field("egaged_service_title_$i"); // FIXED
                    $description = get_field("egaged_service_desc_$i");

                    if (empty($image) && empty($title) && empty($description)) {
                        continue;
                    }
                ?>
                    <div class="item">
                        <div class="ems-card">

                            <?php if (! empty($image)) : ?>
                                <img
                                    class="ems-icon"
                                    src="<?php echo esc_url($image['url']); ?>"
                                    alt="<?php echo esc_attr($image['alt'] ?? $title ?? 'service-icon'); ?>">
                            <?php endif; ?>

                            <?php if (! empty($hover_image)) : ?>
                                <img
                                    class="ems-icon-hover"
                                    src="<?php echo esc_url($hover_image['url']); ?>"
                                    alt="<?php echo esc_attr($hover_image['alt'] ?? $title ?? 'service-icon-hover'); ?>">
                            <?php endif; ?>

                            <?php if (! empty($title)) : ?>
                                <h4 class="ems-title">
                                    <?php echo wp_kses_post($title); ?>
                                </h4>
                            <?php endif; ?>

                            <?php if (! empty($description)) : ?>
                                <p class="ems-desc">
                                    <?php echo wp_kses_post($description); ?>
                                </p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endfor; ?>

            </div><!-- /.carousel -->

        </div>
    </section>
<?php endif; ?>


<?php
$multi_platform_bg_color = get_field('muti_platform_bg_color');
$multi_platform_heading  = get_field('multi_platform_heading');
$carousel_id = 'multi-platform-carousel-' . wp_rand(1000, 9999);
?>

<?php if (! empty($multi_platform_heading)) : ?>
    <section class="business-benefits-multi-platform"
        <?php if (! empty($multi_platform_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($multi_platform_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="common-heading-color">
                        <?php echo esc_html($multi_platform_heading); ?>
                    </h2>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div id="<?php echo esc_attr($carousel_id); ?>"
                        class="owl-carousel owl-theme multi-platform-carousel">

                        <?php
                        for ($i = 1; $i <= 6; $i++) :

                            $icon = get_field("multi_platform_icon_$i");
                            $desc = get_field("multi_platform_desc_$i");

                            if (empty($icon) && empty($desc)) {
                                continue;
                            }
                        ?>
                            <div class="item">
                                <div class="multi-platform-card">

                                    <?php if (! empty($icon)) : ?>
                                        <img
                                            src="<?php echo esc_url($icon['url']); ?>"
                                            alt="<?php echo esc_attr($icon['alt'] ?? 'platform-icon'); ?>">
                                    <?php endif; ?>

                                    <?php if (! empty($desc)) : ?>
                                        <p><?php echo esc_html($desc); ?></p>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endfor; ?>

                    </div>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>


<?php
/* ===== Integrate Your Online (Store Section) ===== */
$store_bg_color = get_field('store_bg_color');
$store_heading  = get_field('store_headimg');
$img_upward     = get_field('upward_img');
$img_downward   = get_field('downward_img');
?>

<?php if (! empty($store_heading)) : ?>
    <section
        class="whats-new-in-store"
        <?php if (! empty($store_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($store_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">

            <!-- Heading -->
            <div class="row">
                <div class="col-md-12">
                    <div class="whats-new-content text-center">
                        <p class="whats-new-heading">
                            <?php echo wp_kses_post($store_heading); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Loop for 6 items -->
            <div class="row">
                <?php
                for ($i = 1; $i <= 3; $i++) :

                    // ACF fields
                    $store_img        = get_field('store_image_' . $i);
                    $store_paragraph  = get_field('store_paragrapgh_' . $i);

                    // Skip if both empty
                    if (empty($store_img) && empty($store_paragraph)) continue;
                ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="what-new-store text-center">

                            <?php if (! empty($store_img['url'])) : ?>
                                <img
                                    src="<?php echo esc_url($store_img['url']); ?>"
                                    alt="<?php echo esc_attr($store_heading); ?>" />
                            <?php endif; ?>

                            <?php if (! empty($store_paragraph)) : ?>
                                <p class="whats-new-store-text">
                                    <?php echo wp_kses_post($store_paragraph); ?>
                                </p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Upward Arrow -->
            <?php if (! empty($img_upward['url'])) : ?>
                <div class="upward-arrow text-center">
                    <img src="<?php echo esc_url($img_upward['url']); ?>" alt="upward-arrow">
                </div>
            <?php endif; ?>

            <!-- Downward Arrow -->
            <?php if (! empty($img_downward['url'])) : ?>
                <div class="downward-arrow text-center">
                    <img src="<?php echo esc_url($img_downward['url']); ?>" alt="downward-arrow">
                </div>
            <?php endif; ?>

        </div>
    </section>
<?php endif; ?>

<?php
/**
 * Engagement Model Section - ACF Template
 * 
 * ACF Field Group: engagement_model_section (Group)
 * ├── section_title         → Text
 * ├── section_description   → Textarea
 * ├── section_bg_color      → Color Picker
 * ├── section_bg_image      → Image (return: array)
 * └── slides                → Repeater
 *     ├── slide_icon        → Image (return: array)
 *     ├── slide_title       → Text
 *     └── slide_description → Textarea
 *
 * Usage: Place inside your page template or flexible content layout.
 * Replace 'engagement_model_section' with your actual ACF group field name.
 */

$enable_carousel = get_field( 'enable_node_engagement_model_carousel' );
$section         = get_field( 'node_engagement_model_content' );
$title           = $section['heading']          ?? '';
$description     = $section['sub_heading']      ?? '';
$bg_color        = $section['background_color'] ?? '';
$bg_image        = $section['background_image'] ?? null;
$hover_bg        = $section['slide_hover_bg_color'] ?? '#1483C6';
$slides          = $section['slides']           ?? [];

// Build inline style for section background
$section_style = '';
if ( ! empty( $bg_image['url'] ) ) {
    $section_style .= 'background-image: url(' . esc_url( $bg_image['url'] ) . '); background-size: cover; background-position: center;';
}
if ( ! empty( $bg_color ) ) {
    $section_style .= 'background-color: ' . esc_attr( $bg_color ) . ';';
}
?>

<?php if ( $enable_carousel && ! empty( $slides ) ) : ?>

<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/slick/slick.min.css">
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/slick/slick-theme.min.css">

<section class="engagement-model-section" style="<?php echo $section_style; ?>">
    <div class="container">

        <?php if ( $title || $description ) : ?>
        <div class="engagement-model-section__header">
            <?php if ( $title ) : ?>
                <h2 class="engagement-model-section__title">
                    <?php echo wp_kses_post( $title ); ?>
                </h2>
            <?php endif; ?>
            <?php if ( $description ) : ?>
                <div class="engagement-model-section__description">
                    <?php echo wp_kses_post( $description ); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="engagement-model-grid">
            <?php foreach ( $slides as $slide ) :
                $icon        = $slide['slide_icon']       ?? null;
                $slide_title = $slide['slide_title']       ?? '';
                $slide_desc  = $slide['slide_description'] ?? '';
            ?>
            <div class="engagement-model-card" data-hover-bg="<?php echo esc_attr( $hover_bg ); ?>">
                <?php if ( ! empty( $icon['url'] ) ) : ?>
                <div class="engagement-model-card__icon">
                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                         alt="<?php echo esc_attr( $icon['alt'] ?? $slide_title ); ?>"
                         loading="lazy" />
                </div>
                <?php endif; ?>
                <?php if ( $slide_title ) : ?>
                <h3 class="engagement-model-card__title"><?php echo esc_html( $slide_title ); ?></h3>
                <?php endif; ?>
                <?php if ( $slide_desc ) : ?>
                <div class="engagement-model-card__description"><?php echo wp_kses_post( $slide_desc ); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/slick/slick.min.js"></script>
<script>
jQuery(document).ready(function ($) {
    var $grid = $('.engagement-model-grid');
    if (!$grid.length || typeof $.fn.slick === 'undefined') return;

    $grid.on('init', function() {
        // Bind hover directly to cards after slick wraps them
        $grid.find('.engagement-model-card').each(function() {
            var $card = $(this);
            var bg = $card.data('hover-bg') || '#1483C6';
            $card.on('mouseenter', function() {
                $card.css('background-color', bg);
            }).on('mouseleave', function() {
                $card.css('background-color', '');
            });
        });
    }).slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        infinite: false,
        speed: 400,
        prevArrow: '<button type="button" class="slick-prev custom-arrow"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.00085 10.0723L6.00819 9.05786L2.73614 5.75566L14.2883 5.75566L14.2883 4.31679L2.73614 4.31679L6.01533 1.01458L5.00086 0.00018038L-9.39766e-05 5.03622L5.00085 10.0723Z" fill="#0070F0"/></svg></button>',
        nextArrow: '<button type="button" class="slick-next custom-arrow"><svg width="15" height="11" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.28748 0L8.28014 1.0144L11.5522 4.31661H0V5.75548H11.5522L8.273 9.05768L9.28748 10.0721L14.2884 5.03604L9.28748 0Z" fill="#0070F0"/></svg></button>',
        responsive: [
            { breakpoint: 1024, settings: { slidesToShow: 2 } },
            { breakpoint: 768,  settings: { slidesToShow: 1 } }
        ]
    });

});
</script>

<?php endif; ?>


<?php
/* ===== Resource Hub Shortcode ===== */
$resource_shortcode = get_field('resource_hub_shortcode');
if (! empty($resource_shortcode)) {
    echo do_shortcode($resource_shortcode);
}
?>

<?php
/* ===== FAQ Section ===== */
$faq_shortcode   = get_field('faq_shortcode');
$faq_background  = get_field('faq_bg_color');
$faq_heading     = get_field('faq_heading');

// Unique ID for second FAQ section wrapper
$faq_unique_id_2 = 'faq_section_two_' . uniqid();
?>

<?php if (!empty($faq_heading)) : ?>
    <section class="java-development-solutions faq-section"
        <?php if (!empty($faq_background)) : ?>
        style="background-color: <?php echo esc_attr($faq_background); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">

                <div class="col-lg-12 text-center">
                    <h2><?php echo wp_kses_post($faq_heading); ?></h2>
                </div>

                <div class="col-lg-12">
                    <div class="dynamics-solutions-faq">
                        <div id="<?php echo esc_attr($faq_unique_id_2); ?>" class="accordion fixspacerT">

                            <?php
                            if (!empty($faq_shortcode)) {

                                // Step 1: Run the shortcode but don't echo yet
                                $raw_html = do_shortcode($faq_shortcode);

                                // Step 2: Append unique ID suffix to each FAQ element
                                $suffix = '_' . uniqid();

                                // Find all IDs inside the shortcode output
                                preg_match_all('/\sid=["\']([^"\']+)["\']/', $raw_html, $matches);
                                $ids = array_unique($matches[1]);

                                // Replace each ID and its references
                                foreach ($ids as $id) {
                                    $new_id = $id . $suffix;

                                    // Replace ID attribute
                                    $raw_html = preg_replace(
                                        '/(\sid=["\'])' . preg_quote($id, '/') . '(["\'])/',
                                        '$1' . $new_id . '$2',
                                        $raw_html
                                    );

                                    // Replace #id references (href, data-bs-target, data-target)
                                    $raw_html = preg_replace(
                                        '/(href|data-bs-target|data-target)=["\']#' . preg_quote($id, '/') . '["\']/',
                                        '$1="#' . $new_id . '"',
                                        $raw_html
                                    );

                                    // Replace aria-controls
                                    $raw_html = preg_replace(
                                        '/(aria-controls)=["\']' . preg_quote($id, '/') . '["\']/',
                                        '$1="' . $new_id . '"',
                                        $raw_html
                                    );

                                    // Replace aria-labelledby
                                    $raw_html = preg_replace(
                                        '/(aria-labelledby)=["\']' . preg_quote($id, '/') . '["\']/',
                                        '$1="' . $new_id . '"',
                                        $raw_html
                                    );
                                }

                                // Step 3: Output the cleaned + fixed HTML
                                echo $raw_html;
                            }
                            ?>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>

<?php
/* ===== Integrate Your Online (Next Section) ===== */
$form_bg_color       = get_field('form_bg_color');
$form_heading        = get_field('form_heading');
$form_thankyou_mesg  = get_field('form_thankyou_mesg');
?>

<?php if (! empty($form_heading)) : ?>
    <section class="want-to-take-your-web"
        <?php if (! empty($form_bg_color)) : ?>
        style="background-color: <?php echo esc_attr($form_bg_color); ?>;"
        <?php endif; ?>>
        <div class="container">
            <div class="row">

                <div class="col-md-12">
                    <div class="dev-plans-heading">
                        <p class="want-web-dev-plans text-center">
                            <?php echo wp_kses_post($form_heading); ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="get-in-touch-form gz-marketo-form gz-mkto-col">

                        <form id="mktoForm_5187"></form>

                        <div class="gz-thanks-message font-24 d_none">
                            <?php echo wp_kses_post($form_thankyou_mesg); ?>
                        </div>

                        <script>
                            MktoForms2.loadForm("//app-lon03.marketo.com", "690-NGB-767", 5187, function(form) {
                                form.onSuccess(function(values, followUpUrl) {
                                    form.getFormElem().hide();
                                    jQuery(".gz-thanks-message").css("display", "block");
                                    jQuery(".get-title").css("display", "none");
                                    jQuery(".get-in-touch").addClass("form-sub");
                                    return false;
                                });
                            });
                        </script>

                    </div>
                </div>

            </div>
        </div>
    </section>
<?php endif; ?>



<?php get_footer('gzInner'); ?>



<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Get all tab groups (each UL.nav-tabs)
        const tabGroups = document.querySelectorAll('.nav-tabs');

        tabGroups.forEach(group => {

            const tabs = group.querySelectorAll('.nav-link');
            const tabContentWrapper = group.parentElement.querySelector('.tab-content');

            tabs.forEach(tab => {

                tab.addEventListener("click", function() {

                    const targetID = this.getAttribute("data-bs-target");

                    // Remove active from all tabs inside THIS group only
                    tabs.forEach(t => t.classList.remove("active"));

                    // Remove active from all tab-pane inside THIS group's tab-content
                    const panes = tabContentWrapper.querySelectorAll('.tab-pane');
                    panes.forEach(p => p.classList.remove("show", "active"));

                    // Activate clicked tab
                    this.classList.add("active");

                    // Show its pane
                    const pane = tabContentWrapper.querySelector(targetID);
                    if (pane) {
                        pane.classList.add("show", "active");
                    }
                });

            });

        });

    });
</script>


<script>
    jQuery(document).ready(function($) {

        /* ============================
           UNIVERSAL OWL INITIALIZER
        ============================ */

        $(".owl-carousel").each(function() {

            let carouselID = $(this).attr("id"); // for debugging
            let classes = $(this).attr("class");

            // Default settings for all
            let settings = {
                loop: true,
                margin: 20,
                nav: true,
                dots: false,
                autoplay: false,
                smartSpeed: 800,
                items: 1,
                responsive: {
                    0: {
                        items: 1
                    },
                    576: {
                        items: 1
                    },
                    768: {
                        items: 1
                    },
                    992: {
                        items: 3
                    }
                }
            };

            /* ============================
               SPECIAL CONFIG BASED ON CLASS
            ============================ */

            // 1️⃣ Healthcare Dashboard
            if (classes.includes("healthcare-dashboard-slider")) {
                settings = {
                    loop: true,
                    margin: 10,
                    nav: true,
                    items: 1,
                    stagePadding: 150,
                    responsive: {
                        0: {
                            items: 1,
                            stagePadding: 60
                        },
                        600: {
                            items: 1,
                            stagePadding: 100
                        },
                        1000: {
                            items: 1,
                            stagePadding: 200
                        },
                        1200: {
                            items: 1,
                            stagePadding: 250
                        }
                    }
                };
            }

            // 2️⃣ Three slide carousel
            if (classes.includes("owl-carousel-three-slide")) {
                settings.responsive = {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 1
                    },
                    992: {
                        items: 3
                    }
                };
            }

            // 3️⃣ Blog carousel
            if (classes.includes("from-our-blog-carousel")) {
                settings.margin = 30;
                settings.items = 3;
            }

            // 4️⃣ Engagement model carousel
            if (classes.includes("engagement-models-carousel")) {
                settings.responsive = {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 1
                    },
                    992: {
                        items: 3
                    },
                };
            }

            // 6️⃣ Why Cards
            if (classes.includes("why-cards-carousel")) {
                settings.margin = 30;
                settings.responsive = {
                    0: { items: 1 },
                    576: { items: 2 },
                    768: { items: 3 },
                    992: { items: 4 }
                };
            }

            // 7️⃣ Development Services Technology Stack
            if (classes.includes("dst-carousel")) {
                settings.margin = 30;
                settings.responsive = {
                    0:   { items: 1 },
                    768: { items: 2 },
                    992: { items: 3 }
                };
            }

            // 5️⃣ Multi Platform
            if (classes.includes("multi-platform-carousel")) {
                settings.items = 3;
                settings.responsive = {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    1200: {
                        items: 3
                    }
                };
            }

            // INIT CAROUSEL
            $(this).owlCarousel(settings);

        });

    });
</script>
<style>
    .why-integrate-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}
a.card-cta-link {
    color: #F8AD1D;
}
    </style>
