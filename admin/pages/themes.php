<script type="text/javascript">
    if (window.WMPJSInterface && window.WMPJSInterface != null){
        jQuery(document).ready(function(){

            WMPJSInterface.localpath = "<?php echo plugins_url()."/".WMP_DOMAIN."/"; ?>";
            WMPJSInterface.init();
        });
    }
</script>
<div id="wmpack-admin">
	<div class="spacer-60"></div>
    <!-- set title -->
    <h1><?php echo WMP_PLUGIN_NAME.' '.WMP_VERSION;?></h1>
	<div class="spacer-20"></div>
	<div class="themes">
        <div class="left-side">

            <!-- add nav menu -->
            <?php include_once(WMP_PLUGIN_PATH.'admin/sections/admin-menu.php'); ?>
            <div class="spacer-0"></div>

            <div class="details theming">
                <h2 class="title">Choose Your Mobile Theme</h2>
                <div class="spacer_15"></div>
                <div class="spacer-15"></div>
                <div class="themes">

                    <?php
                        $arr_themes = array(
                        	array(
								'title'=> 'Obliq',
								'icon' => plugins_url().'/'.WMP_DOMAIN.'/admin/images/theme-obliq.jpg',
								'selected' => 1
							)
                        );

						$arr_themes = array_merge($arr_themes, WMobilePack_Admin::upgrade_pro_themes());

						foreach ($arr_themes as $theme):

							$buy_link = isset($theme['buy_link']) ? $theme['buy_link']: '';
                    ?>

                        <div class="theme <?php echo $buy_link != '' ? 'premium' : '';?>">
                            <div class="corner relative <?php echo isset($theme['selected']) ? 'active' : '';?>">
                                <div class="indicator"></div>
                            </div>
                            <div class="image" style="background:url(<?php echo isset($theme['icon']) ? esc_attr( $theme['icon'] ) : '' ?>);">
                                <div class="relative">
									<?php if (!isset($theme['selected']) || $theme['selected'] == 0): ?>
										<div class="overlay">
											<div class="spacer-100"></div>
											<div class="actions">
												<div class="preview" id="wmp_themes_preview"></div>
											</div>
											<div class="spacer-10"></div>
											<div class="text-preview">Preview theme</div>
										</div>
									<?php endif; ?>
                                </div>
                            </div>
                            <div class="name">
								<?php echo isset($theme['title']) ? $theme['title'] : '';?>
							</div>
							<div class="content">
								<?php if ($buy_link != ''):?>
									<a href="<?php echo $buy_link != '' ? esc_attr($buy_link) : ''; ?>" class="btn orange smaller" target="_blank">
										<span class="shopping"></span>&nbsp;
										<?php echo $theme['buy_text'];?>
									</a>
								<?php endif ?>
							</div>
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
            <div class="spacer-10"></div>

        </div>

        <div class="right-side">
            <!-- waitlist form -->
            <?php include_once(WMP_PLUGIN_PATH.'admin/sections/waitlist.php'); ?>

            <!-- add feedback form -->
            <?php include_once(WMP_PLUGIN_PATH.'admin/sections/feedback.php'); ?>
        </div>
	</div>
</div>

<script type="text/javascript">
    if (window.WMPJSInterface && window.WMPJSInterface != null){
        jQuery(document).ready(function(){
            window.WMPJSInterface.add("UI_previewthemesgallery","WMP_THEMES_GALLERY",{'DOMDoc':window.document, 'baseThemeUrl': '<?php echo plugins_url()."/".WMP_DOMAIN.'/frontend/themes/app1';?>'}, window);
        });
    }
</script>