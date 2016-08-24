(function() {
    'use strict';

    // Custom JavaScript

    var btnSwedish = $('.btn-swedish'),
        btnEnglish = $('.btn-english'),
        btnLang = $('.lang-btn'),
        i18n = domI18n({
            selector: '[data-translatable]',
            separator: ' // ',
            languages: ['se', 'gb'],
            defaultLanguage: 'se',
            currentLanguage: (store.has('blekan_app_lang')) ? store.get('blekan_app_lang') : 'se'
        });

    if (store.has('blekan_app_lang')) {
        switch (store.get('blekan_app_lang')) {
            case "gb":
                switchLang('gb');
                break;
            case "se":
                switchLang('se');
                break;
        }
    }

    btnEnglish.on('click', function() {
        switchLang('gb');
    });

    btnSwedish.on('click', function() {
        switchLang('se');
    });

    function switchLang(lang) {
        switch (lang) {
            case "gb":
                i18n.changeLanguage('gb');
                btnEnglish.parent().addClass('active');
                btnSwedish.parent().removeClass('active');
                btnLang.children('.flag-icon').removeClass('flag-icon-se').addClass('flag-icon-gb');
                store('blekan_app_lang', 'gb');
                break;
            case "se":
                i18n.changeLanguage('se');
                btnSwedish.parent().addClass('active');
                btnEnglish.parent().removeClass('active');
                btnLang.children('.flag-icon').removeClass('flag-icon-gb').addClass('flag-icon-se');
                store('blekan_app_lang', 'se');
                break;
            default:
                i18n.changeLanguage('se');
                btnSwedish.parent().addClass('active');
                btnEnglish.parent().removeClass('active');
                btnLang.children('.flag-icon').removeClass('flag-icon-gb').addClass('flag-icon-se');
                store('blekan_app_lang', 'se');
                break;
        }
    }

    $("#share").jsSocials({
        shares: ["twitter", "facebook", "googleplus", "linkedin"],
        shareIn: "blank",
        url: "http://blekan.se",
        showLabel: true,
        showCount: true,
    });

    $('.button-collapse').sideNav();
    $('.parallax').parallax();
    $('.collapsible').collapsible();
    $('select').material_select();
    $('.dropdown-button').dropdown();

    $('.slider').slider({
        full_width: true,
        height: 700,
        interval: 2000,
        transition: 400,
    });

    $(".slider").css("height", function() {
        return $(".indicators").height() + $(".slides").height() + 10;
    });

    $('.modal-trigger').leanModal({
        dismissible: true,
    });

})();

$(document).ready(function() {
    if ($('#flash-modal').length) {
        $('#flash-modal').openModal();
    }
    var bLazy = new Blazy({
        selector: 'img' // all images
    });
});
