<?php
/*
 * @ https://github.com/CMSNTSourceCode
 * @ Meo Mat Cang
 * @ PHP 7.4
 * @ Telegram : @Mo_Ho_Bo
 */
if(!defined("IN_SITE")) {
    exit("The Request Not Found");
}
echo "\n\n<body>\n\n    <!-- Start Switcher -->\n    <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"switcher-canvas\" aria-labelledby=\"offcanvasRightLabel\">\n        <div class=\"offcanvas-header border-bottom\">\n            <h5 class=\"offcanvas-title text-default\" id=\"offcanvasRightLabel\">Switcher</h5>\n            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>\n        </div>\n        <div class=\"offcanvas-body\">\n            <nav class=\"border-bottom border-block-end-dashed\">\n                <div class=\"nav nav-tabs nav-justified\" id=\"switcher-main-tab\" role=\"tablist\">\n                    <button class=\"nav-link active\" id=\"switcher-home-tab\" data-bs-toggle=\"tab\"\n                        data-bs-target=\"#switcher-home\" type=\"button\" role=\"tab\" aria-controls=\"switcher-home\"\n                        aria-selected=\"true\">Theme Styles</button>\n                    <button class=\"nav-link\" id=\"switcher-profile-tab\" data-bs-toggle=\"tab\"\n                        data-bs-target=\"#switcher-profile\" type=\"button\" role=\"tab\" aria-controls=\"switcher-profile\"\n                        aria-selected=\"false\">Theme Colors</button>\n                </div>\n            </nav>\n            <div class=\"tab-content\" id=\"nav-tabContent\">\n                <div class=\"tab-pane fade show active border-0\" id=\"switcher-home\" role=\"tabpanel\"\n                    aria-labelledby=\"switcher-home-tab\" tabindex=\"0\">\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Theme Color Mode:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-light-theme\">\n                                        Light\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"theme-style\"\n                                        id=\"switcher-light-theme\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-dark-theme\">\n                                        Dark\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"theme-style\"\n                                        id=\"switcher-dark-theme\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Directions:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-ltr\">\n                                        LTR\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"direction\" id=\"switcher-ltr\"\n                                        checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-rtl\">\n                                        RTL\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"direction\" id=\"switcher-rtl\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Navigation Styles:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-vertical\">\n                                        Vertical\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-style\"\n                                        id=\"switcher-vertical\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-horizontal\">\n                                        Horizontal\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-style\"\n                                        id=\"switcher-horizontal\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"navigation-menu-styles\">\n                        <p class=\"switcher-style-head\">Vertical & Horizontal Menu Styles:</p>\n                        <div class=\"row switcher-style gx-0 pb-2 gy-2\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-menu-click\">\n                                        Menu Click\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-menu-styles\"\n                                        id=\"switcher-menu-click\">\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-menu-hover\">\n                                        Menu Hover\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-menu-styles\"\n                                        id=\"switcher-menu-hover\">\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-icon-click\">\n                                        Icon Click\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-menu-styles\"\n                                        id=\"switcher-icon-click\">\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-icon-hover\">\n                                        Icon Hover\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"navigation-menu-styles\"\n                                        id=\"switcher-icon-hover\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"sidemenu-layout-styles\">\n                        <p class=\"switcher-style-head\">Sidemenu Layout Styles:</p>\n                        <div class=\"row switcher-style gx-0 pb-2 gy-2\">\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-default-menu\">\n                                        Default Menu\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-default-menu\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-closed-menu\">\n                                        Closed Menu\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-closed-menu\">\n                                </div>\n                            </div>\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-icontext-menu\">\n                                        Icon Text\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-icontext-menu\">\n                                </div>\n                            </div>\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-icon-overlay\">\n                                        Icon Overlay\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-icon-overlay\">\n                                </div>\n                            </div>\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-detached\">\n                                        Detached\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-detached\">\n                                </div>\n                            </div>\n                            <div class=\"col-sm-6\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-double-menu\">\n                                        Double Menu\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"sidemenu-layout-styles\"\n                                        id=\"switcher-double-menu\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Page Styles:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-regular\">\n                                        Regular\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"page-styles\"\n                                        id=\"switcher-regular\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-classic\">\n                                        Classic\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"page-styles\"\n                                        id=\"switcher-classic\">\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-modern\">\n                                        Modern\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"page-styles\"\n                                        id=\"switcher-modern\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Layout Width Styles:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-full-width\">\n                                        Full Width\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"layout-width\"\n                                        id=\"switcher-full-width\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-boxed\">\n                                        Boxed\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"layout-width\"\n                                        id=\"switcher-boxed\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Menu Positions:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-menu-fixed\">\n                                        Fixed\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"menu-positions\"\n                                        id=\"switcher-menu-fixed\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-menu-scroll\">\n                                        Scrollable\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"menu-positions\"\n                                        id=\"switcher-menu-scroll\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Header Positions:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-header-fixed\">\n                                        Fixed\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"header-positions\"\n                                        id=\"switcher-header-fixed\" checked>\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-header-scroll\">\n                                        Scrollable\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"header-positions\"\n                                        id=\"switcher-header-scroll\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                    <div class=\"\">\n                        <p class=\"switcher-style-head\">Loader:</p>\n                        <div class=\"row switcher-style gx-0\">\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-loader-enable\">\n                                        Enable\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"page-loader\"\n                                        id=\"switcher-loader-enable\">\n                                </div>\n                            </div>\n                            <div class=\"col-4\">\n                                <div class=\"form-check switch-select\">\n                                    <label class=\"form-check-label\" for=\"switcher-loader-disable\">\n                                        Disable\n                                    </label>\n                                    <input class=\"form-check-input\" type=\"radio\" name=\"page-loader\"\n                                        id=\"switcher-loader-disable\" checked>\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"tab-pane fade border-0\" id=\"switcher-profile\" role=\"tabpanel\"\n                    aria-labelledby=\"switcher-profile-tab\" tabindex=\"0\">\n                    <div>\n                        <div class=\"theme-colors\">\n                            <p class=\"switcher-style-head\">Menu Colors:</p>\n                            <div class=\"d-flex switcher-style pb-2\">\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-white\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Light Menu\" type=\"radio\" name=\"menu-colors\"\n                                        id=\"switcher-menu-light\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-dark\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Dark Menu\" type=\"radio\" name=\"menu-colors\"\n                                        id=\"switcher-menu-dark\" checked>\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Color Menu\" type=\"radio\" name=\"menu-colors\"\n                                        id=\"switcher-menu-primary\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-gradient\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Gradient Menu\" type=\"radio\" name=\"menu-colors\"\n                                        id=\"switcher-menu-gradient\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-transparent\"\n                                        data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Transparent Menu\"\n                                        type=\"radio\" name=\"menu-colors\" id=\"switcher-menu-transparent\">\n                                </div>\n                            </div>\n                            <div class=\"px-4 pb-3 text-muted fs-11\">Note:If you want to change color Menu dynamically\n                                change from below Theme Primary color picker</div>\n                        </div>\n                        <div class=\"theme-colors\">\n                            <p class=\"switcher-style-head\">Header Colors:</p>\n                            <div class=\"d-flex switcher-style pb-2\">\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-white\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Light Header\" type=\"radio\" name=\"header-colors\"\n                                        id=\"switcher-header-light\" checked>\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-dark\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Dark Header\" type=\"radio\" name=\"header-colors\"\n                                        id=\"switcher-header-dark\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Color Header\" type=\"radio\" name=\"header-colors\"\n                                        id=\"switcher-header-primary\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-gradient\" data-bs-toggle=\"tooltip\"\n                                        data-bs-placement=\"top\" title=\"Gradient Header\" type=\"radio\"\n                                        name=\"header-colors\" id=\"switcher-header-gradient\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-transparent\"\n                                        data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Transparent Header\"\n                                        type=\"radio\" name=\"header-colors\" id=\"switcher-header-transparent\">\n                                </div>\n                            </div>\n                            <div class=\"px-4 pb-3 text-muted fs-11\">Note:If you want to change color Header dynamically\n                                change from below Theme Primary color picker</div>\n                        </div>\n                        <div class=\"theme-colors\">\n                            <p class=\"switcher-style-head\">Theme Primary:</p>\n                            <div class=\"d-flex flex-wrap align-items-center switcher-style\">\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary-1\" type=\"radio\"\n                                        name=\"theme-primary\" id=\"switcher-primary\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary-2\" type=\"radio\"\n                                        name=\"theme-primary\" id=\"switcher-primary1\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary-3\" type=\"radio\"\n                                        name=\"theme-primary\" id=\"switcher-primary2\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary-4\" type=\"radio\"\n                                        name=\"theme-primary\" id=\"switcher-primary3\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-primary-5\" type=\"radio\"\n                                        name=\"theme-primary\" id=\"switcher-primary4\">\n                                </div>\n                                <div class=\"form-check switch-select ps-0 mt-1 color-primary-light\">\n                                    <div class=\"theme-container-primary\"></div>\n                                    <div class=\"pickr-container-primary\"></div>\n                                </div>\n                            </div>\n                        </div>\n                        <div class=\"theme-colors\">\n                            <p class=\"switcher-style-head\">Theme Background:</p>\n                            <div class=\"d-flex flex-wrap align-items-center switcher-style\">\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-bg-1\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-background\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-bg-2\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-background1\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-bg-3\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-background2\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-bg-4\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-background3\">\n                                </div>\n                                <div class=\"form-check switch-select me-3\">\n                                    <input class=\"form-check-input color-input color-bg-5\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-background4\">\n                                </div>\n                                <div\n                                    class=\"form-check switch-select ps-0 mt-1 tooltip-static-demo color-bg-transparent\">\n                                    <div class=\"theme-container-background\"></div>\n                                    <div class=\"pickr-container-background\"></div>\n                                </div>\n                            </div>\n                        </div>\n                        <div class=\"menu-image mb-3\">\n                            <p class=\"switcher-style-head\">Menu With Background Image:</p>\n                            <div class=\"d-flex flex-wrap align-items-center switcher-style\">\n                                <div class=\"form-check switch-select m-2\">\n                                    <input class=\"form-check-input bgimage-input bg-img1\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-bg-img\">\n                                </div>\n                                <div class=\"form-check switch-select m-2\">\n                                    <input class=\"form-check-input bgimage-input bg-img2\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-bg-img1\">\n                                </div>\n                                <div class=\"form-check switch-select m-2\">\n                                    <input class=\"form-check-input bgimage-input bg-img3\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-bg-img2\">\n                                </div>\n                                <div class=\"form-check switch-select m-2\">\n                                    <input class=\"form-check-input bgimage-input bg-img4\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-bg-img3\">\n                                </div>\n                                <div class=\"form-check switch-select m-2\">\n                                    <input class=\"form-check-input bgimage-input bg-img5\" type=\"radio\"\n                                        name=\"theme-background\" id=\"switcher-bg-img4\">\n                                </div>\n                            </div>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"d-grid canvas-footer\">\n                    <a href=\"javascript:void(0);\" id=\"reset-all\" class=\"btn btn-danger m-1\">Reset</a>\n                </div>\n            </div>\n        </div>\n    </div>\n    <!-- End Switcher -->\n\n\n    <!-- Loader -->\n    <div id=\"loader\">\n        <img src=\"";
echo base_url("public/theme/");
echo "assets/images/media/loader.svg\" alt=\"\">\n    </div>\n    <!-- Loader -->\n\n    <div class=\"page\">\n        <!-- app-header -->\n        <header class=\"app-header\">\n\n            <!-- Start::main-header-container -->\n            <div class=\"main-header-container container-fluid\">\n\n                <!-- Start::header-content-left -->\n                <div class=\"header-content-left\">\n\n                    <!-- Start::header-element -->\n                    <div class=\"header-element\">\n                        <div class=\"horizontal-logo\">\n                            <a href=\"";
echo base_url_admin();
echo "\" class=\"header-logo\">\n                                <img src=\"";
echo base_url("assets/img/cmsnt_light.png");
echo "\" alt=\"logo\" class=\"desktop-logo\">\n                                <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-logo\">\n                                <img src=\"";
echo base_url("assets/img/cmsnt_dark.png");
echo "\" alt=\"logo\" class=\"desktop-dark\">\n                                <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-dark\">\n                                <img src=\"";
echo base_url("assets/img/cmsnt_light.png");
echo "\" alt=\"logo\"\n                                    class=\"desktop-white\">\n                                <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-white\">\n                            </a>\n                        </div>\n                    </div>\n                    <!-- End::header-element -->\n\n                    <!-- Start::header-element -->\n                    <div class=\"header-element\">\n                        <!-- Start::header-link -->\n                        <a aria-label=\"Hide Sidebar\"\n                            class=\"sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle\"\n                            data-bs-toggle=\"sidebar\" href=\"javascript:void(0);\"><span></span></a>\n                        <!-- End::header-link -->\n                    </div>\n                    <!-- End::header-element -->\n                </div>\n                <!-- End::header-content-left -->\n\n                <!-- Start::header-content-right -->\n                <div class=\"header-content-right\">\n                    <div class=\"header-element header-search\">\n                        <!-- Start::header-link -->\n                        <a href=\"";
echo base_url();
echo "\" class=\"header-link\">\n                            <i class=\"bx bx-user-circle header-link-icon\"></i>\n                        </a>\n                        <!-- End::header-link -->\n                    </div>\n\n\n                    <!-- Start::header-element -->\n                    <div class=\"header-element header-theme-mode\">\n                        <!-- Start::header-link|layout-setting -->\n                        <a href=\"javascript:void(0);\" class=\"header-link layout-setting\">\n                            <span class=\"light-layout\">\n                                <!-- Start::header-link-icon -->\n                                <i class=\"bx bx-moon header-link-icon\"></i>\n                                <!-- End::header-link-icon -->\n                            </span>\n                            <span class=\"dark-layout\">\n                                <!-- Start::header-link-icon -->\n                                <i class=\"bx bx-sun header-link-icon\"></i>\n                                <!-- End::header-link-icon -->\n                            </span>\n                        </a>\n                        <!-- End::header-link|layout-setting -->\n                    </div>\n                    <!-- End::header-element -->\n\n\n\n\n\n\n                    <!-- Start::header-element -->\n                    <div class=\"header-element header-fullscreen\">\n                        <!-- Start::header-link -->\n                        <a onclick=\"openFullscreen();\" href=\"#\" class=\"header-link\">\n                            <i class=\"bx bx-fullscreen full-screen-open header-link-icon\"></i>\n                            <i class=\"bx bx-exit-fullscreen full-screen-close header-link-icon d-none\"></i>\n                        </a>\n                        <!-- End::header-link -->\n                    </div>\n                    <!-- End::header-element -->\n\n\n\n\n                    <!-- Start::header-element -->\n                    <div class=\"header-element\">\n                        <!-- Start::header-link|switcher-icon -->\n                        <a href=\"#\" class=\"header-link switcher-icon\" data-bs-toggle=\"offcanvas\"\n                            data-bs-target=\"#switcher-canvas\">\n                            <i class=\"bx bx-cog header-link-icon\"></i>\n                        </a>\n                        <!-- End::header-link|switcher-icon -->\n                    </div>\n                    <!-- End::header-element -->\n\n                </div>\n                <!-- End::header-content-right -->\n\n            </div>\n            <!-- End::main-header-container -->\n\n        </header>\n        <!-- /app-header -->\n        <!-- Start::app-sidebar -->\n        <aside class=\"app-sidebar sticky\" id=\"sidebar\">\n\n            <!-- Start::main-sidebar-header -->\n            <div class=\"main-sidebar-header\">\n                <a href=\"";
echo base_url_admin();
echo "\" class=\"header-logo\">\n                    <img src=\"";
echo base_url("assets/img/cmsnt_light.png");
echo "\" alt=\"logo\" class=\"desktop-logo\">\n                    <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-logo\">\n                    <img src=\"";
echo base_url("assets/img/cmsnt_dark.png");
echo "\" alt=\"logo\" class=\"desktop-dark\">\n                    <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-dark\">\n                    <img src=\"";
echo base_url("assets/img/cmsnt_light.png");
echo "\" alt=\"logo\" class=\"desktop-white\">\n                    <img src=\"";
echo base_url("assets/img/icon cmsnt.jpeg");
echo "\" alt=\"logo\" class=\"toggle-white\">\n                </a>\n            </div>\n            <!-- End::main-sidebar-header -->\n\n            <!-- Start::main-sidebar -->\n            <div class=\"main-sidebar\" id=\"sidebar-scroll\">\n\n                <!-- Start::nav -->\n                <nav class=\"main-menu-container nav nav-pills flex-column sub-open\">\n                    <div class=\"slide-left\" id=\"slide-left\">\n                        <svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"#7b8191\" width=\"24\" height=\"24\"\n                            viewBox=\"0 0 24 24\">\n                            <path d=\"M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z\"></path>\n                        </svg>\n                    </div>\n                    <ul class=\"main-menu\">\n                        <li class=\"slide__category\"><span class=\"category-name\">Main</span></li>\n                        <li class=\"slide\">\n                            <a href=\"";
echo base_url_admin("home");
echo "\"\n                                class=\"side-menu__item ";
echo active_sidebar(["home", ""]);
echo "\">\n                                <i class=\"bx bxs-dashboard side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Dashboard</span>\n                            </a>\n                        </li>\n                        <li class=\"slide has-sub ";
echo show_sidebar(["logs", "transactions", "log-auto-bank", "telegram-logs", "admin-logs", "failed-attempts-logs"]);
echo "\">\n                            <a href=\"javascript:void(0);\"\n                                class=\"side-menu__item ";
echo show_sidebar(["logs", "transactions"]);
echo "\">\n                                <i class='bx bx-history side-menu__icon'></i>\n                                <span class=\"side-menu__label\">Lịch sử</span>\n                                <i class=\"fe fe-chevron-right side-menu__angle\"></i>\n                            </a>\n                            <ul class=\"slide-menu child1\">\n                                ";
if(checkPermission($getUser["admin"], "view_logs")) {
    echo "                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("logs");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["logs"]);
    echo "\">Nhật ký hoạt\n                                        động</a>\n                                </li>\n                                ";
}
echo "                                ";
if(checkPermission($getUser["admin"], "view_transactions")) {
    echo "                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("transactions");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["transactions"]);
    echo "\">Biến động\n                                        số dư</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("log-auto-bank");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["log-auto-bank"]);
    echo "\">Lịch sử ngân\n                                        hàng</a>\n                                </li>\n                                ";
}
echo "                                ";
if(checkPermission($getUser["admin"], "view_telegram_logs")) {
    echo "                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("telegram-logs");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["telegram-logs"]);
    echo "\">Telegram Logs</a>\n                                </li>\n                                ";
}
echo "                                ";
if($getUser["admin"] == 99999) {
    echo "                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("admin-logs");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["admin-logs"]);
    echo "\">Admin Logs</a>\n                                </li>\n                                ";
}
echo "                                ";
if(checkPermission($getUser["admin"], "view_block_ip")) {
    echo "                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("failed-attempts-logs");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["failed-attempts-logs"]);
    echo "\">Failed Attempts Logs</a>\n                                </li>\n                                ";
}
echo "                            </ul>\n                        </li>\n                        ";
if(checkPermission($getUser["admin"], "view_automations")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("automations");
    echo "\" \n                                class=\"side-menu__item ";
    echo active_sidebar(["automations", "automation-edit"]);
    echo "\">\n                                <i class=\"bx bxs-calendar side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Tự động hóa</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        <li class=\"slide__category\"><span class=\"category-name\">Bảo mật</span></li>\n                        ";
if(checkPermission($getUser["admin"], "view_block_ip")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("block-ip");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["block-ip"]);
    echo "\">\n                                <i class=\"bx bx-block side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Block IP</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        <li class=\"slide__category\"><span class=\"category-name\">Dịch vụ</span></li>\n                        ";
if(checkPermission($getUser["admin"], "view_product")) {
    echo "                        <li\n                            class=\"slide has-sub ";
    echo show_sidebar(["category-add", "product-warehouse", "product-sold", "product-api-manager", "product-api-edit", "product-api-add", "product-api", "product-orders", "categories", "category-edit", "products", "product-add", "product-edit", "product-stock"]);
    echo "\">\n                            <a href=\"javascript:void(0);\"\n                                class=\"side-menu__item ";
    echo show_sidebar(["category-add", "product-warehouse", "product-sold", "product-api-manager", "product-api-edit", "product-api-add", "product-api", "product-orders", "categories", "category-edit", "products", "product-add", "product-edit", "product-stock"]);
    echo "\">\n                                <i class='bx bx-cart side-menu__icon'></i>\n                                <span class=\"side-menu__label\">Sản phẩm</span>\n                                <i class=\"fe fe-chevron-right side-menu__angle\"></i>\n                            </a>\n                            <ul class=\"slide-menu child1\">\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("categories");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["category-add", "categories", "category-edit"]);
    echo "\">Chuyên\n                                        mục</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("products");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["products", "product-add", "product-edit", "product-stock"]);
    echo "\">Tất\n                                        cả sản phẩm</a>\n                                </li>\n                                ";
    if(checkPermission($getUser["admin"], "manager_suppliers")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("product-api");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["product-api-manager", "product-api-edit", "product-api", "product-api-add"]);
        echo "\">Kết\n                                        nối API</a>\n                                </li>\n                                ";
    }
    echo "                                ";
    if(checkPermission($getUser["admin"], "view_orders_product")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("product-orders");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["product-orders"]);
        echo "\">Đơn hàng</a>\n                                </li>\n                                ";
    }
    echo "                                ";
    if(checkPermission($getUser["admin"], "view_sold_product")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("product-sold");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["product-sold"]);
        echo "\">Tài khoản đã bán</a>\n                                </li>\n                                ";
    }
    echo "                                ";
    if(checkPermission($getUser["admin"], "edit_stock_product")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("product-warehouse");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["product-warehouse"]);
        echo "\">Tài khoản trong kho</a>\n                                </li>\n                                ";
    }
    echo "                            </ul>\n                        </li>\n                        ";
}
echo "                        <li class=\"slide__category\"><span class=\"category-name\">Quản lý</span></li>\n                        ";
if(checkPermission($getUser["admin"], "view_user")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("users");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["users", "user-edit"]);
    echo "\">\n                                <i class=\"bx bxs-user side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Thành viên</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_role")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("roles");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["roles", "role-edit"]);
    echo "\">\n                                <i class=\"bx bxs-check-shield side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Admin Role</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_recharge")) {
    echo "                        <li\n                            class=\"slide has-sub ";
    echo show_sidebar(["recharge-thesieure", "recharge-flutterwave", "recharge-momo", "recharge-card", "recharge-bank", "recharge-crypto", "recharge-bank-edit", "recharge-paypal", "recharge-perfectmoney", "recharge-toyyibpay", "recharge-squadco", "recharge-bank-config", "recharge-manual", "recharge-manual-edit", "recharge-xipay", "recharge-korapay", "recharge-tmweasyapi", "recharge-bakong", "recharge-openpix"]);
    echo "\">\n                            <a href=\"javascript:void(0);\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["recharge-thesieure", "recharge-flutterwave", "recharge-momo", "recharge-card", "recharge-bank", "recharge-crypto", "recharge-bank-edit", "recharge-paypal", "recharge-perfectmoney", "recharge-toyyibpay", "recharge-squadco", "recharge-bank-config", "recharge-manual", "recharge-manual-edit", "recharge-xipay", "recharge-korapay", "recharge-tmweasyapi", "recharge-bakong", "recharge-openpix"]);
    echo "\">\n                                <i class='bx bxs-wallet-alt side-menu__icon'></i>\n                                <span class=\"side-menu__label\">Nạp tiền</span>\n                                <i class=\"fe fe-chevron-right side-menu__angle\"></i>\n                            </a>\n                            <ul class=\"slide-menu child1\">\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-bank");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-bank", "recharge-bank-edit", "recharge-bank-config"]);
    echo "\">Ngân\n                                        hàng</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-card");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-card"]);
    echo "\">Nạp thẻ cào</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-crypto");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-crypto"]);
    echo "\">Crypto USDT</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-momo");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-momo"]);
    echo "\">Ví MOMO</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-thesieure");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-thesieure"]);
    echo "\">Ví THESIEURE</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-paypal");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-paypal"]);
    echo "\">Paypal</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-perfectmoney");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-perfectmoney"]);
    echo "\">Perfect\n                                        Money</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-toyyibpay");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-toyyibpay"]);
    echo "\">Toyyibpay\n                                        Malaysia</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-squadco");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-squadco"]);
    echo "\">Squadco\n                                        Nigeria</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-flutterwave");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-flutterwave"]);
    echo "\">Flutterwave</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-xipay");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-xipay"]);
    echo "\">XiPay (AliPay, WechatPay)</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-korapay");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-korapay"]);
    echo "\">Korapay Africa</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-tmweasyapi");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-tmweasyapi"]);
    echo "\">Tmweasyapi Thailand</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-openpix");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-openpix"]);
    echo "\">OpenPix Brazil</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-bakong");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-bakong"]);
    echo "\">Bakong Wallet Cambodia</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("recharge-manual");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["recharge-manual", "recharge-manual-edit"]);
    echo "\">Manual Payment</a>\n                                </li>\n                            </ul>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_affiliate")) {
    echo "                        <li\n                            class=\"slide has-sub ";
    echo show_sidebar(["affiliate-config", "affiliate-withdraw", "affiliate-history"]);
    echo "\">\n                            <a href=\"javascript:void(0);\"\n                                class=\"side-menu__item ";
    echo show_sidebar(["affiliate-config", "affiliate-withdraw", "affiliate-history"]);
    echo "\">\n                                <i class='bx bx-group side-menu__icon'></i>\n                                <span class=\"side-menu__label\">Affiliate Program</span>\n                                <i class=\"fe fe-chevron-right side-menu__angle\"></i>\n                            </a>\n                            <ul class=\"slide-menu child1\">\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("affiliate-history");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["affiliate-history"]);
    echo "\">Nhật\n                                        ký hoa hồng</a>\n                                </li>\n                                ";
    if(checkPermission($getUser["admin"], "view_withdraw_affiliate")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("affiliate-withdraw");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["affiliate-withdraw"]);
        echo "\">Rút\n                                        tiền\n                                        ";
        $total_widthdraw_pending = $CMSNT->get_row(" SELECT COUNT(id) FROM `aff_withdraw` WHERE `status` = 'pending' ")["COUNT(id)"];
        if(0 < $total_widthdraw_pending) {
            echo "                                        <span\n                                            class=\"badge bg-warning-transparent ms-2\">";
            echo $total_widthdraw_pending;
            echo "</span>\n                                        ";
        }
        echo "                                    </a>\n                                </li>\n                                ";
    }
    echo "                                ";
    if(checkPermission($getUser["admin"], "edit_affiliate")) {
        echo "                                <li class=\"slide\">\n                                    <a href=\"";
        echo base_url_admin("affiliate-config");
        echo "\"\n                                        class=\"side-menu__item ";
        echo active_sidebar(["affiliate-config"]);
        echo "\">Cấu\n                                        hình</a>\n                                </li>\n                                ";
    }
    echo "                            </ul>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_email_campaigns")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("email-campaigns");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["email-campaigns", "email-campaign-add", "email-campaign-edit", "email-sending-view"]);
    echo "\">\n                                <i class=\"bx bx-mail-send side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Email Campaigns</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_coupon")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("coupons");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["coupons"]);
    echo "\">\n                                <i class=\"bx bxs-discount side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Mã giảm giá</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_promotion")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("promotions");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["promotions"]);
    echo "\">\n                                <i class=\"fa-solid fa-percent side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Khuyến mãi nạp tiền</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_blog")) {
    echo "                        <li\n                            class=\"slide has-sub ";
    echo show_sidebar(["blog-add", "blogs", "blog-edit", "blog-category", "blog-category-edit"]);
    echo "\">\n                            <a href=\"javascript:void(0);\"\n                                class=\"side-menu__item ";
    echo show_sidebar(["blog-add", "blogs", "blog-edit", "blog-category", "blog-category-edit"]);
    echo "\">\n                                <i class='bx bxl-blogger side-menu__icon'></i>\n                                <span class=\"side-menu__label\">Bài viết</span>\n                                <i class=\"fe fe-chevron-right side-menu__angle\"></i>\n                            </a>\n                            <ul class=\"slide-menu child1\">\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("blog-add");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["blog-add"]);
    echo "\">Viết bài mới</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("blogs");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["blogs", "blog-edit"]);
    echo "\">Tất cả bài\n                                        viết</a>\n                                </li>\n                                <li class=\"slide\">\n                                    <a href=\"";
    echo base_url_admin("blog-category");
    echo "\"\n                                        class=\"side-menu__item ";
    echo active_sidebar(["blog-category", "blog-category-edit"]);
    echo "\">Chuyên\n                                        mục</a>\n                                </li>\n                            </ul>\n                        </li>\n                        ";
}
echo "                        <li class=\"slide__category\"><span class=\"category-name\">Cài đặt hệ thống</span></li>\n                        ";
if(checkPermission($getUser["admin"], "view_menu")) {
    echo "                        <!-- <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("menu-list");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["menu-list", "menu-edit"]);
    echo "\">\n                                <i class=\"bx bx-sitemap side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Menu</span>\n                            </a>\n                        </li> -->\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_lang")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("language-list");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["language-list", "language-add", "language-edit", "translate-list"]);
    echo "\">\n                                <i class=\"las la-language side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Ngôn ngữ</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "view_currency")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("currency-list");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["currency-list", "currency-add", "currency-edit"]);
    echo "\">\n                                <i class=\"bx bx-dollar side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Tiền tệ</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "edit_theme")) {
    echo "                        <li class=\"slide\">\n                            <a href=\"";
    echo base_url_admin("theme");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["theme"]);
    echo "\">\n                                <i class=\"bx bxs-image side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Giao diện</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                        ";
if(checkPermission($getUser["admin"], "edit_setting")) {
    echo "                        <li class=\"slide mb-5\">\n                            <a href=\"";
    echo base_url_admin("settings");
    echo "\"\n                                class=\"side-menu__item ";
    echo active_sidebar(["settings"]);
    echo "\">\n                                <i class=\"bx bx-cog side-menu__icon\"></i>\n                                <span class=\"side-menu__label\">Cài đặt</span>\n                            </a>\n                        </li>\n                        ";
}
echo "                    </ul>\n                    <div class=\"slide-right\" id=\"slide-right\"><svg xmlns=\"http://www.w3.org/2000/svg\" fill=\"#7b8191\"\n                            width=\"24\" height=\"24\" viewBox=\"0 0 24 24\">\n                            <path d=\"M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z\"></path>\n                        </svg></div>\n                </nav>\n                <!-- End::nav -->\n\n            </div>\n            <!-- End::main-sidebar -->\n\n        </aside>\n        <!-- End::app-sidebar -->\n\n        <script>\n        function changeLanguage(id) {\n            \$.ajax({\n                url: \"";
echo BASE_URL("ajaxs/client/update.php");
echo "\",\n                method: \"POST\",\n                dataType: \"JSON\",\n                data: {\n                    action: 'changeLanguage',\n                    id: id\n                },\n                success: function(result) {\n                    if (result.status == 'success') {\n                        showMessage(result.msg, result.status);\n                        location.reload();\n                    } else {\n                        showMessage(result.msg, result.status);\n                    }\n                },\n                error: function() {\n                    alert(html(result));\n                    history.back();\n                }\n            });\n        }\n        </script>\n        <script>\n        function changeCurrency(id) {\n            \$.ajax({\n                url: \"";
echo BASE_URL("ajaxs/client/update.php");
echo "\",\n                method: \"POST\",\n                dataType: \"JSON\",\n                data: {\n                    action: 'changeCurrency',\n                    id: id\n                },\n                success: function(result) {\n                    if (result.status == 'success') {\n                        showMessage(result.msg, result.status);\n                        location.reload();\n                    } else {\n                        showMessage(result.msg, result.status);\n                    }\n                },\n                error: function() {\n                    alert(html(result));\n                    history.back();\n                }\n            });\n        }\n        </script>";

?>