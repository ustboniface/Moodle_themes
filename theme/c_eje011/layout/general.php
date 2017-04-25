<?php
/**
 * ************************************************************************
 * *                  Saint Boniface - TRADUCTION                        **
 * ************************************************************************
 * @package     theme                                                    **
 * @subpackage  boniface_trad                                            **
 * @name        boniface_trad                                            **
 * @copyright   oohoo.biz                                                **
 * @link        http://oohoo.biz                                         **
 * @author      Nicolas Bretin                                           **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * ************************************************************************
 * ************************************************************************ */
$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
$showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);
$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepre && !$showsidepost)
{
    $bodyclasses[] = 'side-pre-only';
}
else if ($showsidepost && !$showsidepre)
{
    $bodyclasses[] = 'side-post-only';
}
else if (!$showsidepost && !$showsidepre)
{
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu)
{
    $bodyclasses[] = 'has_custom_menu';
}
if ($hasnavbar)
{
    $bodyclasses[] = 'hasnavbar';
}

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter']))
{
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter']))
    {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

echo $OUTPUT->doctype()
?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
    <head>
        <title><?php echo $PAGE->title ?></title>
        <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme') ?>" />
        <?php echo $OUTPUT->standard_head_html() ?>
    </head>
    <body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses . ' ' . join(' ', $bodyclasses)) ?>">
        <?php echo $OUTPUT->standard_top_of_body_html() ?>

        <div id="page">
            <?php if ($hasheading || $hasnavbar || !empty($courseheader))
            {
                ?>
                <div id="page-header">
                    <?php if ($hasheading)
                    {
                        ?>
                        
                        <div class="headerinfotop"><a class="headerlink" href="http://www.ustboniface.ca/page.aspx?pid=479">Baccalauréat spécialisé en traduction</a></div>
                        <div class="clearfix"></div>
                        <a class="headerlink" href="<?php echo $CFG->wwwroot; ?>">
                            <h1 class="headermain"><span class="headertitle1">ÉCOLE DE TRADUCTION</span><br/><span class="headertitle2"><?php echo $PAGE->heading ?></span></h1>
                        </a>
                        <div class="headermenu"><?php
                            echo $OUTPUT->login_info();
                            if (!empty($PAGE->layout_options['langmenu']))
                            {
                                echo $OUTPUT->lang_menu();
                            }
                            echo $PAGE->headingmenu
                            ?></div><?php } ?>

                    <?php if (!empty($courseheader))
                    {
                        ?>
                        <div id="course-header"><?php echo $courseheader; ?></div>
                    <?php } ?>
                    <?php if ($hascustommenu)
                    {
                        ?>
                        <div id="custommenu"><?php echo $custommenu; ?></div>
    <?php } ?>

    <?php if ($hasnavbar)
    {
        ?>
                        <div class="navbar clearfix">
                            <div class="breadcrumb"><?php echo $OUTPUT->navbar(); ?></div>
                            <div class="navbutton"><?php echo $PAGE->button; ?></div>
                        </div>
    <?php } ?>
                </div>
<?php } ?>
            <!-- END OF HEADER -->

            <div id="page-content">
                <div id="region-main-box">
                    <div id="region-post-box">

                        <div id="region-main-wrap">
                            <div id="region-main">
                                <div class="region-content">
<?php echo $coursecontentheader; ?>
                        <?php echo $OUTPUT->main_content() ?>
                        <?php echo $coursecontentfooter; ?>
                                </div>
                            </div>
                        </div>

<?php if ($hassidepre)
{
    ?>
                            <div id="region-pre" class="block-region">
                                <div class="region-content">
                            <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                                </div>
                            </div>
                                <?php } ?>

                        <?php if ($hassidepost)
                        {
                            ?>
                            <div id="region-post" class="block-region">
                                <div class="region-content">
    <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                                </div>
                            </div>
            <?php } ?>

                    </div>
                </div>
            </div>

            <!-- START OF FOOTER -->
<?php if (!empty($coursefooter))
{
    ?>
                <div id="course-footer"><?php echo $coursefooter; ?></div>
<?php } ?>
<?php if ($hasfooter)
{
    ?>
                <div id="page-footer" class="clearfix">
                    <div class="footerinfoleft">
                        <br/>
                        ©<?php echo date('Y') ?> Université de Saint-Boniface
                    </div>
                    <div class="footerinforight">
                        
                    </div>
                    <div class="clearfix">
                    </div>
    <?php
    echo $OUTPUT->standard_footer_html();
    ?>
                </div>
<?php } ?>
            <div class="clearfix"></div>
        </div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
    </body>
</html>