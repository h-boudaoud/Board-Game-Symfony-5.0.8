let displayHeader = true;

$(document).ready(function () {
    changeHeight();
    $(window).bind('resizeEnd', function () {
        console.log('change : ', $('body').height());
        changeHeight();
    });

    $(window).resize(function () {
        if (this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function () {
            $(this).trigger('resizeEnd');
        }, 100);

    });

    $(".js-starRating").each(function () {
        console.log('starRating 19 ->', $(this));
        $(this).html(starRating($(this).text()));
    });

    $(".js_image_saveTo").change(function () {
        const newClass = "fa-" + $(this).val();
        const oldClass = "fa-" + (($(this).val() === "folder") ? "database" : "folder");
        $("#saveTo i").removeClass(oldClass).addClass(newClass);
        console.log($(this).val());
    });
});

// Bootstrap 4 Responsive Dropdown Multi Submenu
$(function () {
    $('.dropdown-menu a.dropdown-toggle').on('click', function () {
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').find('.show').removeClass("show");
        }
        let $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass('show'); // appliqué au ul
        $(this).parent().toggleClass('show'); // appliqué au li parent

        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function () {
            $('.dropdown-submenu .show').removeClass("show") // appliqué au ul
                .removeClass("show"); // appliqué au li parent
        });
        return false;
    });
});


// funsctions js

//nav bar Layout
function changeDisplayHeader() {
    displayHeader = !displayHeader;
    const displayHeaderClass = displayHeader ? 'fas fa-level-up-alt' : 'fas fa-level-down-alt';
    $("#navHeader").css('display', displayHeader ? '' : 'none');
    $("#displayHeader >i+i").attr('class', displayHeaderClass);
    changeHeight()
}

// height block section
function changeHeight() {
    $("#content, body > section").css(
        'min-height',
        ($('html').height() - ($('body > header').height() + $('body > footer').height() + 20)) + 'px');

    $("body").css('padding-bottum', $("body > footer").height() + 'px')
        .css('padding-top', $("body > header").height() + 'px');
    //$("#navigationLeft").css('top', $("body > header").height() + 'px')


}


function starRating(value) {
    console.log('starRating ->', value);

    let htmlCode = (value) + ' : ';
    if(value.trim() !== '') {
        for (let i = 1; i <= 5; i++) {
            if (i -.25 <= value) {

                htmlCode += '<i class="fas fa-star" style="color:goldenrod"></i>'
            } else if (i - .75 < value) {
                htmlCode += '<i class="fas fa-star-half-alt" style="color:goldenrod"></i>'
            } else {
                htmlCode += '<i class="far fa-star"></i>'
            }
            console.log(i, '\t', value, '\n--', htmlCode);
        }
    }else {
        htmlCode +=  '<i class="far fa-star px-3 badge badge-secondary"'+
            'style="text-decoration: line-through;"></i>';
    }

    console.log('starRating', htmlCode);
    return htmlCode
}


