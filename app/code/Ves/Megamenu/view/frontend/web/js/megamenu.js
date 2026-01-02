define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'scrolltofixed',
        'jquery.drilldown'
    ],
    function ($, _, registry) {
        'use strict';

        $.widget(
            'ves.megamenu',
            {
                options: {
                    isDrillDown: false,
                    id: '',
                    navId: '',
                    blockId: '',
                    googleLink: '',
                    event: "hover",
                    desktopTemplate: 'horizontal',
                    mobileTemplate: 'accordion',
                    scrolltofixed: false,
                    updatePriceBox: true,
                    allowClickOpenner: true,
                    toggleNav: true,
                    drilldownSpeed: 300,
                    maxMobileSize: 768,
                    maxTabletSize: 1024
                },
                _create: function () {
                    var options = this.options;
                    var desktopTemplate = this.getDesktopTemplate();
                    var mobileTemplate = this.getMobileTemplate();
                    if (desktopTemplate == "drill" || mobileTemplate == "drill") {
                        options.isDrillDown = true;
                    }
                    if (options.isDrillDown) {
                        this._initializeDrillDown();
                    }
                    if (options.googleLink) {
                        this._initGoogle();
                    }
                    if (options.navId && options.blockId && options.id) {
                        this._initMegamenu();
                    }
                },
                _initializeDrillDown: function () {
                    var htmlDrillOpener = '<span class="drill-opener"></span>';
                    var htmlDrillDown = '<div class="drilldown-back"><a href="#"><span class="drill-opener"></span><span class="current-cat"></span></a></div>';
                    if ($(".ves-megamenu .before-ves-submenu-inner").length) {
                        $(".ves-megamenu .before-ves-submenu-inner").parent().prepend($(htmlDrillDown));
                    }
                    if ($(".ves-megamenu .nav-anchor > .opener").length) {
                        $(".ves-megamenu .nav-anchor > .opener").parent().append($(htmlDrillOpener));
                    }
                },
                _initGoogle: function () {
                    var self = this;
                    var ss  = document.createElement("link");
                    ss.type = "text/css";
                    ss.rel  = "stylesheet";
                    ss.href = self.options.googleLink;
                    document.getElementsByTagName("head")[0].appendChild(ss);
                },
                _initMegamenu: function () {
                    var self = this;
                    var desktopTemplate = this.getDesktopTemplate();

                    if (this.options.updatePriceBox) {
                        $('.price-box', $("#"+self.options.navId)).each(function(){
                            $(this).removeClass('price-box').addClass('price-box1');
                            $(this).attr('data-role','priceBox1');
                        })
                    }
                    if (self.options.event == 'hover' && desktopTemplate != 'accordion' && desktopTemplate != 'drill') {
                        this._initDesktopHoverMenu();
                    }
                    if (self.options.event == 'click') {
                        this._initClickMenuItem();
                    }
                    if (desktopTemplate == 'accordion') {
                        this._initDesktopAccordion();
                    }

                    if (self.options.allowClickOpenner) {
                        $('#'+self.options.navId+' .opener').on('click', function(e) {
                            e.preventDefault();
                            $("#"+self.options.navId+" .nav-item").removeClass("item-active");
                            var parent = $(this).parents(".nav-item").eq(0);
                            $(this).toggleClass('item-active');
                            var subMenu = $(parent).find(".submenu").eq(0);
                            subMenu.stop().slideToggle();
                            subMenu.css({'height':''});
                            return false;
                        });
                    }

                    $("#"+self.options.blockId+" .ves-navtoggle"+self.options.blockId).click(function() {
                        $('html').removeClass('nav-before-open nav-open');
                        $('.ves-overlay'+self.options.blockId).show();
                        $("#"+self.options.blockId).append('<div class="ves-overlay ves-overlay'+self.options.blockId+'"></div>');
                        $('#'+self.options.navId).css("left", "0px");
                        if ($('html').hasClass('ves-navopen')) {
                            $('html').removeClass('ves-navopen');
                            setTimeout(function () {
                                $('html').removeClass('ves-nav-before-open');
                            }, 300);
                        }	 else {
                            $('html').addClass('ves-nav-before-open');
                            setTimeout(function () {
                                $('html').addClass('ves-navopen');
                            }, 42);
                        }
                    });

                    $(document).on("click", ".ves-overlay"+self.options.blockId, function(){
                        $('#'+self.options.navId).css("left","");
                        $('html').removeClass('ves-navopen');
                        setTimeout(function () {
                            $('html').removeClass('ves-nav-before-open');
                        }, 300);
                        $(this).remove();
                        return false;
                    });

                    if (self.options.scrolltofixed) {
                        $('.section-items.nav-sections-items').scrollToFixed({
                            zIndex: 999
                        });
                    }

                    this._initResizeWindow();

                    if (self.options.toggleNav) {
                        this._initToggleNav();
                    }
                },
                _initDesktopHoverMenu: function() {
                    var self = this;
                    var desktopTemplate = this.getDesktopTemplate();

                    $("#"+self.options.navId+" .nav-item").hover(function() {
                        $(this).addClass('current');
                        var id = $(this).data("dynamic-id");
                        if (id) {
                            $(this).parents(".dynamic-items").find("li").removeClass("dynamic-active");
                            $(this).addClass("dynamic-active");
                            $("#"+self.options.navId+" ."+id).parent().find(".dynamic-item").removeClass("dynamic-active");
                            $("#"+self.options.navId+" ."+id).addClass("dynamic-active");
                        }

                        if ($(this).data('hovericon')) {
                            $(this).children('.nav-anchor').find('.item-icon').attr('src', $(this).data('hovericon'));
                        }
                        if ($(this).data('caret') && $(this).data('hovercaret')) {
                            $(this).children('.nav-anchor').find('.ves-caret').removeClass($(this).data('caret')).addClass($(this).data('hovercaret'));
                        }

                        var child_anchor = $(this).children('.nav-anchor');
                        $(child_anchor).css({
                            "background-color": $(child_anchor).data("hover-bgcolor"),
                            "color": $(child_anchor).data("hover-color")
                        });
                        if (desktopTemplate == 'horizontal') {
                            if ($(this).hasClass('level0')) {
                                var mParentTop = $('#'+self.options.blockId+' .navigation').offset().top;
                                var mParentHeight = $(this).parent().height();
                                var mTop =  $(this).height();
                                var mHeight = $(this).height();
                                var mParent = $(this).parent();
                                if (mHeight < mParentHeight) {
                                    mTop = $(this).offset().top - mParent.offset().top + mHeight;
                                }
                                $(this).children('.submenu').css({top:mTop});
                            }
                        }
                    }, function() {
                        var id = $(this).attr('id');
                        if ($(this).data('iconsrc')) {
                            $(this).children('.nav-anchor').find('.item-icon').attr('src', $(this).data('iconsrc'));
                        }
                        if ($(this).data('caret')) {
                            $(this).children('.nav-anchor').find('.ves-caret').removeClass($(this).data('hovercaret')).addClass($(this).data('caret'));
                        }
                        $(this).removeClass('current');
                        var child_anchor = $(this).children('.nav-anchor');
                        var link_bgcolor = $(child_anchor).data("bgcolor");
                        var link_color = $(child_anchor).data("color");

                        if(!link_bgcolor || typeof(link_bgcolor) == 'undefined') {
                            link_bgcolor = 'inherit';
                        }
                        if(!link_color || typeof(link_color) == "undefined") {
                            link_color = 'none';
                        }

                        $(child_anchor).css({
                            "background-color": link_bgcolor,
                            "color": link_color
                        });
                    })
                },
                _initDesktopAccordion: function() {
                    var self = this;
                    $('#'+self.options.navId+' .ves-caret').on('click', function(e) {
                        e.preventDefault();
                        var parent = $(this).parents(".nav-item").eq(0);
                        var subMenu = $(parent).children(".submenu");
                        if (!$(this).hasClass('item-active')) {
                            if ($(parent).data('caret') && $(parent).data('hovercaret')) {
                                $(this).removeClass($(parent).data('caret')).addClass($(parent).data('hovercaret'));
                            }
                                $(this).addClass('item-active');
                                subMenu.stop().slideToggle();
                                setTimeout(function(){
                                    subMenu.css({'height':''});
                                }, 200);
                            } else {
                                subMenu.stop().slideToggle();

                                $(this).removeClass('item-active');
                                $(this).removeClass($(parent).data('hovercaret')).addClass($(parent).data('caret'));
                            }
                            return false;
                    });
                },
                _initResizeWindow: function () {
                    var self = this;
                    var desktopTemplate = this.getDesktopTemplate();
                    var mobileTemplate = this.getMobileTemplate();
                    var mobileDrill, desktopDrill;

                    $(window).on("resize", function() {
                        var disable_bellow_number = $("#"+self.options.navId).attr("data-disable-bellow");
                        if(disable_bellow_number) {
                            if ($(window).width() < parseInt(disable_bellow_number)) {
                                $("#"+self.options.blockId).hide();
                            } else {
                                $("#"+self.options.blockId).show();
                            }
                        }
                        if ($(window).width() < self.options.maxMobileSize) {
                            $("#"+self.options.blockId).addClass("nav-mobile").removeClass('nav-desktop');
                            if (desktopTemplate == 'drill') {
                                $('#'+self.options.blockId+' .drill-opener').hide();
                                $('#'+self.options.blockId+'').css('width', '');
                                if (typeof(desktopDrill) === 'object') {
                                    $('#'+self.options.blockId+'').drilldown('reset');
                                }
                            }
                            if (mobileTemplate == 'drill') {
                                $('#'+self.options.blockId+' .drill-opener').show();
                                mobileDrill = $('#'+self.options.blockId+'').drilldown({
                                    selector: '.drill-opener',
                                    cssClass: {
                                        container: 'navitaion'+self.options.id + '> .navigation',
                                        root: self.options.navId,
                                        sub: 'submenu',
                                        back: 'drilldown-back'
                                    },
                                    speed: self.options.drilldownSpeed
                                });
                            }
                            $('#'+self.options.blockId+' .opener').removeClass('item-active');
                        } else {
                            $("#"+self.options.blockId+" .submenu").css({'display':''});
                            $("#"+self.options.blockId+"").removeClass("nav-mobile").addClass('nav-desktop');
                            if (mobileTemplate == 'drill') {
                                $('#'+self.options.blockId+' .drill-opener').hide();
                                $('#'+self.options.blockId+'').css('width', '');
                                if (typeof(mobileDrill) === 'object') {
                                    $('#'+self.options.blockId+'').drilldown('reset');
                                }

                                $('#'+self.options.blockId+'').find('.submenu').each(function() {
                                    var subWidth = $(this).data('width');
                                    if (!subWidth) {
                                        subWidth = '100%';
                                    }
                                    $(this).css('width', subWidth);
                                });
                            }
                            if (desktopTemplate == 'drill') {
                                $('#'+self.options.blockId+' .drill-opener').show();
                                $('#'+self.options.blockId+'').drilldown({
                                    selector: '.drill-opener',
                                    cssClass: {
                                        container: 'navitaion'+self.options.id,
                                        root: self.options.navId,
                                        sub: 'submenu',
                                        back: 'drilldown-back'
                                    },
                                    speed: self.options.drilldownSpeed
                                });
                            }
                        }

                        $("#"+self.options.navId+" .nav-item").click(function() {
                            if ($(window).width() >= self.options.maxMobileSize && $(window).width() < self.options.maxTabletSize) {
                                var parent = $(this).parent();
                                if ($(parent).children('.submenu').length == 1) {
                                    if ($(parent).hasClass('level0')) {
                                        if ($(parent).hasClass('current')) {
                                            $(parent).removeClass('current');
                                            return false;
                                        }
                                        $("#"+self.options.navId+ ".level0").removeClass('current');
                                        var mParent = $('#'+self.options.blockId+' .navigation').offset().top;
                                        var mHeight = $(parent).offset().top;
                                        var mTop    = (mHeight - mParent) + $(parent).height();
                                        $(parent).children('.submenu').css({top:mTop});
                                    }
                                    $(parent).addClass('current');
                                    if ($(parent).hasClass('submenu-alignleft') || $(parent).hasClass('submenu-alignright')){
                                        if (($(parent).offset().left + $(parent).children('.submenu').width()) > $(window).width()) {
                                            $(parent).children('.submenu').css('max-width','100%');
                                            $(parent).css('position','static');
                                        }
                                    }
                                    return false;
                                }
                            } else {
                                $("#"+self.options.navId).find('.submenu').css('max-width','');
                                $("#"+self.options.navId).find('.submenu-alignleft').css('position','relative');
                            }
                        })
                    }).resize();

                },
                _initToggleNav: function () {
                    var self = this;
                    var toggle_nav = $("#"+self.options.navId).attr("data-toggle-mobile-nav");

                    if(toggle_nav == true || toggle_nav == 'true' || toggle_nav == 1) {
                        if(!$('html').hasClass('megamenu-init-toggle')) {
                            $('html').addClass('megamenu-init-toggle');
                            $(document).on("click", ".action.nav-toggle", function () {
                                if ($('html').hasClass('nav-open')) {
                                    $('html').removeClass('nav-open');
                                    setTimeout(function () {
                                        $('html').removeClass('nav-before-open');
                                    }, 300);
                                } else {
                                    $('html').addClass('nav-before-open');
                                    setTimeout(function () {
                                        $('html').addClass('nav-open');
                                    }, 42);
                                }
                            });
                        }
                    }
                },
                _initClickMenuItem: function() {
                    var self = this;
                    var desktopTemplate = this.getDesktopTemplate();

                    if($("#"+self.options.navId+" .nav-item").length > 0) {
                        $(document).mouseup(function(e) {
                            var container = $("#"+self.options.navId+" .nav-item.level0.current");
                            var container1 = $("#"+self.options.navId+" .nav-item.level1.current");
                            var container2 = $("#"+self.options.navId+" .nav-item.level2.current");
                            var container3 = $("#"+self.options.navId+" .nav-item.level3.current");

                            if (!container.is(e.target) && container.has(e.target).length === 0)
                            {
                                $(container).removeClass('current');
                                $(container).find(".nav-anchor").removeClass("actived");
                                if ($(container).data('caret')) {
                                    $(container).children('.nav-anchor').find('.ves-caret').removeClass($(container).data('hovercaret')).addClass($(container).data('caret'));
                                }
                                if($(container).find(".nav-item.current").length > 0){
                                    $(container).find(".nav-item.current").removeClass("current");
                                }
                                return;
                            }
                            if (!container1.is(e.target) && container1.has(e.target).length === 0)
                            {
                                $(container1).removeClass('current');
                                $(container1).find(".nav-anchor").removeClass("actived");
                                if($(container1).find(".nav-item.current").length > 0){
                                    $(container1).find(".nav-item.current").removeClass("current");
                                }
                                return;
                            }
                            if (!container2.is(e.target) && container2.has(e.target).length === 0)
                            {
                                $(container2).removeClass('current');
                                $(container2).find(".nav-anchor").removeClass("actived");
                                if($(container2).find(".nav-item.current").length > 0){
                                    $(container2).find(".nav-item.current").removeClass("current");
                                }
                                return;
                            }
                            if (!container3.is(e.target) && container3.has(e.target).length === 0)
                            {
                                $(container3).removeClass('current');
                                $(container3).find(".nav-anchor").removeClass("actived");
                                if($(container3).find(".nav-item.current").length > 0){
                                    $(container3).find(".nav-item.current").removeClass("current");
                                }
                                return;
                            }
                        })
                        $("#"+self.options.navId+" .nav-item > .nav-anchor").click(function(e) {
                            if($(this).hasClass("actived")) {
                                if ($(window).width() < self.options.maxMobileSize) {
                                    return true;
                                }
                                var obj = $(this).parents(".nav-item").eq(0);
                                if ($(obj).children('.submenu').length > 0 && !$(obj).hasClass("subgroup")) {
                                    e.preventDefault();
                                    $(this).removeClass("actived");
                                    if($(obj).hasClass('current')) {
                                        $(obj).removeClass('current');
                                    }
                                    if ($(obj).data('caret')) {
                                        $(obj).children('.nav-anchor').find('.ves-caret').removeClass($(obj).data('hovercaret')).addClass($(obj).data('caret'));
                                    }

                                    var container = $("#"+self.options.navId+" .nav-item.level0.current");
                                    var container1 = $("#"+self.options.navId+" .nav-item.level1.current");
                                    var container2 = $("#"+self.options.navId+" .nav-item.level2.current");
                                    var container3 = $("#"+self.options.navId+" .nav-item.level3.current");

                                    if (!container.is(e.target) && container.has(e.target).length === 0)
                                    {
                                        $(container).removeClass('current');
                                        return false;
                                    }
                                    if (!container1.is(e.target) && container1.has(e.target).length === 0)
                                    {
                                        $(container1).removeClass('current');
                                        return false;
                                    }
                                    if (!container2.is(e.target) && container2.has(e.target).length === 0)
                                    {
                                        $(container2).removeClass('current');
                                        return false;
                                    }
                                    if (!container3.is(e.target) && container3.has(e.target).length === 0)
                                    {
                                        $(container3).removeClass('current');
                                        return false;
                                    }
                                    return false;
                                }
                            } else {
                                var obj = $(this).parents(".nav-item").eq(0);
                                if ($(obj).children('.submenu').length > 0 && !$(obj).hasClass("subgroup")) {
                                    if ($(obj).hasClass('level0')) {
                                        $('#' +self.options.navId+  '> .nav-item').removeClass('current');
                                    }
                                    if($(obj).hasClass('current')) {
                                        $(obj).removeClass('current');
                                    } else {
                                        $(obj).addClass('current');
                                        $(this).addClass("actived");
                                    }

                                    if ($(obj).data('hovericon')) {
                                        $(obj).children('.nav-anchor').find('.item-icon').attr('src', $(obj).data('hovericon'));
                                    }
                                    if ($(obj).data('caret') && $(obj).data('hovercaret')) {
                                        $(obj).children('.nav-anchor').find('.ves-caret').removeClass($(obj).data('caret')).addClass($(obj).data('hovercaret'));
                                    }

                                    return false;
                                }
                            }
                        });
                        $("#"+self.options.navId+" .nav-item").hover(function() {
                            var id = $(this).data("dynamic-id");
                            if (id) {
                                $(this).addClass('current');
                                $(this).parents(".dynamic-items").find("li").removeClass("dynamic-active");
                                $(this).addClass("dynamic-active");
                                $("#"+self.options.navId+" ."+id).parent().find(".dynamic-item").removeClass("dynamic-active");
                                $("#"+self.options.navId+" ."+id).addClass("dynamic-active");

                                if ($(this).data('hovericon')) {
                                    $(this).children('.nav-anchor').find('.item-icon').attr('src', $(this).data('hovericon'));
                                }
                                if ($(this).data('caret') && $(this).data('hovercaret')) {
                                    $(this).children('.nav-anchor').find('.ves-caret').removeClass($(this).data('caret')).addClass($(this).data('hovercaret'));
                                }

                                var child_anchor = $(this).children('.nav-anchor');
                                $(child_anchor).css({
                                    "background-color": $(child_anchor).data("hover-bgcolor"),
                                    "color": $(child_anchor).data("hover-color")
                                });

                                if (desktopTemplate == 'horizontal') {
                                if ($(this).hasClass('level0')) {
                                    var mParentTop = $('#'+self.options.blockId+' .navigation').offset().top;
                                    var mParentHeight = $(this).parent().height();
                                    var mTop =  $(this).height();
                                    var mHeight = $(this).height();
                                    var mParent = $(this).parent();
                                    if (mHeight < mParentHeight) {
                                        mTop = $(this).offset().top - mParent.offset().top + mHeight;
                                    }
                                    $(this).children('.submenu').css({top:mTop});
                                }
                                }
                            }
                        }, function() {
                            var id = $(this).data("dynamic-id");
                            if(id) {
                                if ($(this).data('iconsrc')) {
                                    $(this).children('.nav-anchor').find('.item-icon').attr('src', $(this).data('iconsrc'));
                                }
                                if ($(this).data('caret')) {
                                    $(this).children('.nav-anchor').find('.ves-caret').removeClass($(this).data('hovercaret')).addClass($(this).data('caret'));
                                }
                                $(this).removeClass('current');
                                var child_anchor = $(this).children('.nav-anchor');
                                var link_bgcolor = $(child_anchor).data("bgcolor");
                                var link_color = $(child_anchor).data("color");
                                if(!link_bgcolor || typeof(link_bgcolor) == 'undefined') {
                                    link_bgcolor = 'inherit';
                                }

                                $(child_anchor).css({
                                    "background-color": link_bgcolor,
                                    "color": link_color
                                });
                            }
                        });
                    }
                },
                getDesktopTemplate: function() {

                    var availableTemplates = ["horizontal", "accordion", "drill", "vertical"];
                    if ($.inArray(this.options.desktopTemplate, availableTemplates) != -1) {
                        return this.options.desktopTemplate;
                    }
                    return "horizontal";
                },
                getMobileTemplate: function() {

                    var availableTemplates = ["offcanvas", "accordion", "drill"];
                    if ($.inArray(this.options.mobileTemplate, availableTemplates) != -1) {
                        return this.options.mobileTemplate;
                    }
                    return "accordion";
                },
            }
        );

        return $.ves.megamenu;
    }
);
