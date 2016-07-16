justifiedLayout = require('justified-layout');
imageGallery    = {
    'init': function () {
        imageGallery.buildGallery();

        // rebuild the gallery if the window size changes
        $(window).resize(function () {
            imageGallery.buildGallery();
        });
    },

    buildGallery: function () {
        var container  = $('#gallery'),
            outerWidth = container.outerWidth();
        imageData = container.data('images');

        var layoutGeometry = justifiedLayout(
            imageData,
            {containerWidth: outerWidth, fullWidthBreakoutRowCadence: 2}
        );
        
        container.empty();
        for (var i = 0; i < layoutGeometry.boxes.length; i++) {
            var box  = layoutGeometry.boxes[i],
                item = $('<div class="item">'
                    + '<img class="materialboxed" src="/img/'
                    + imageData[i].id + '_medium.jpg">'
                    + '</div>'
                );
            item.css({
                "top":    box.top,
                "left":   box.left,
                "height": box.height,
                "width":  box.width
            });
            container.append(item);
        }
        container.css('height', layoutGeometry.containerHeight);
        $('.materialboxed').materialbox();
    }
};