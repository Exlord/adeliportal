$(document).ready(function(){

    $('#multizoom1').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
        descArea: '#description', // description selector (optional - but required if descriptions are used) - new
        speed: 1500, // duration of fade in for new zoomable images (in milliseconds, optional) - new
        descpos: true, // if set to true - description position follows image position at a set distance, defaults to false (optional) - new
        imagevertcenter: true, // zoomable image centers vertically in its container (optional) - new
        magvertcenter: true, // magnified area centers vertically in relation to the zoomable image (optional) - new
        zoomrange: [3, 10],
        magnifiersize: [500, 300],
        magnifierpos: 'left',
        cursorshadecolor: '#fdffd5',
        cursorshade: true //<-- No comma after last option!
    });

//    $('#image1').addimagezoom({ // single image zoom
//        zoomrange: [3, 10],
//        magnifiersize: [300, 300],
//        magnifierpos: 'right',
//        cursorshade: true,
//        largeimage: 'hayden.jpg' //<-- No comma after last option!
//    });
//
//
//    $('#image2').addimagezoom(); // single image zoom with default options
//
//
//
//    $('#multizoom2').addimagezoom({ // multi-zoom: options same as for previous Featured Image Zoomer's addimagezoom unless noted as '- new'
//        descArea: '#description2', // description selector (optional - but required if descriptions are used) - new
//        disablewheel: true // even without variable zoom, mousewheel will not shift image position while mouse is over image (optional) - new
//        //^-- No comma after last option!
//    });

});
