var NpTypographyParser = {};

NpTypographyParser.parse = function (loaderIframe) {
    var win = loaderIframe.contentWindow,
        doc = loaderIframe.contentDocument,
        container = jQuery(doc).find('#np-test-container')[0] || doc.body;

    var typography = {},
        fonts = {text: {}, heading: {}},
        colors = {},
        fontWeights = {};

    typography.htmlBaseSize = parseFloat(win.getComputedStyle(doc.body.parentElement).fontSize);
    typography.htmlBaseSize = typography.htmlBaseSize > 16 ? typography.htmlBaseSize : 16;
    var headingsFontFamilyCount = {};

    [
        {
            sample: '<p>TEST</p>',
            prop: 'text'
        },
        {
            sample: '<a href="#">TEST</a>',
            prop: 'link'
        },
        {
            sample: '<h1>TEST</h1>',
            prop: 'h1'
        },
        {
            sample: '<h2>TEST</h2>',
            prop: 'h2'
        },
        {
            sample: '<h3>TEST</h3>',
            prop: 'h3'
        },
        {
            sample: '<h4>TEST</h4>',
            prop: 'h4'
        },
        {
            sample: '<h5>TEST</h5>',
            prop: 'h5'
        },
        {
            sample: '<h6>TEST</h6>',
            prop: 'h6'
        }
    ].forEach(function (item) {
        var prop = item.prop;

        container.innerHTML = item.sample;
        var style = win.getComputedStyle(container.children[0]);

        var value = {};
        typography[prop] = value;

        value['font-size'] = parseFloat(style.fontSize) / typography.htmlBaseSize;

        if (style.fontStyle !== 'normal') {
            value['font-style'] = style.fontStyle;
        }

        value['font-weight'] = style.fontWeight;
        fontWeights[style.fontWeight] = true;

        if (style.letterSpacing !== 'normal') {
            value['letter-spacing'] = parseFloat(style.letterSpacing).toString();
        }

        var lineHeight = parseFloat(style.lineHeight) / parseFloat(style.fontSize);
        if (!isNaN(lineHeight)) {
            value['line-height'] = lineHeight.toString();
        }

        if (style.textDecorationLine !== 'none') {
            value['text-decoration'] = style.textDecorationLine;
        }

        if (style.textTransform !== 'none') {
            value['text-transform'] = style.textTransform;
        }

        var fontFamily = style.fontFamily.replace(/"/g, '').split(',').map(Function.prototype.call, String.prototype.trim);
        if (fontFamily[0]) {
            value['font-family'] = fontFamily[0] + ', ' + (fontFamily[1] || 'sans-serif');
        }

        if (prop === 'link') {
            var excludeColors = [
                'rgb(0, 0, 0)', '#000000', '#000', // exclude black colors
                'rgb(128, 128, 128)', '#808080', '#999999', // exclude gray colors
                'rgb(255, 255, 255)', '#ffffff', '#fff', // exclude white colors
            ];
            if (!excludeColors.includes(style.color)) {
                colors.color1 = style.color;
            }
        }
        if (prop === 'text') {
            colors.textColor = style.color;
        }
    });

    var i;
    for (i = 1; i <= 6; i++) {
        var fontFamily = typography['h' + i]['font-family'];
        if (fontFamily) {
            headingsFontFamilyCount[fontFamily] = (headingsFontFamilyCount[fontFamily] || 0) + 1;
        }
    }

    var headingsFontFamilyCountMax = Math.max.apply(null, Object.values(headingsFontFamilyCount));
    for (var ff in headingsFontFamilyCount) {
        if (headingsFontFamilyCount.hasOwnProperty(ff)) {
            if (headingsFontFamilyCountMax === headingsFontFamilyCount[ff]) {
                fonts.heading['font-family'] = ff.split(', ')[0];
                fonts.heading['font-fallback'] = ff.split(', ')[1];
                break;
            }
        }
    }

    if (typography.text['font-family']) {
        fonts.text['font-family'] = typography.text['font-family'].split(', ')[0];
        fonts.text['font-fallback'] = typography.text['font-family'].split(', ')[1];
    }

    jQuery.each(typography, function (type, value) {
        var baseFontFamily = type.match(/h\d/) ?
            fonts.heading['font-family'] + ', ' + fonts.heading['font-fallback'] :
            fonts.text['font-family'] + ', ' + fonts.text['font-fallback'];

        if (value['font-family'] === baseFontFamily) {
            delete value['font-family'];
        }
    });

    [fonts.text, fonts.heading].forEach(function (obj) {
        jQuery(doc).find('link').each(function () {
            var link = decodeURIComponent(this.href);
            var fontFamily = obj['font-family'].replace(/\s/g, '+') + ':';
            if (link.indexOf(fontFamily) !== -1) {
                var match = link.split(fontFamily)[1].match(/(\d+i?,?)+/g);
                if (match && match[0]) {
                    obj['font-family'] += ':' + match[0];
                }
            }
        });
    });

    for (var bgContainer = container; bgContainer; bgContainer = bgContainer.parentElement) {
        var bgColor = win.getComputedStyle(bgContainer).backgroundColor;
        if ("rgba(0, 0, 0, 0)" !== bgColor) {
            colors.bgColor = bgColor;
            break;
        }
    }

    typography.link['text-color'] = "palette-1-base";

    return {
        themeTypography: typography,
        themeColors: colors,
        themeFonts: fonts
    };
};