let displayHeader = false;
let cart = [];
let user = 'anon';
let sessionCart = null;

$(document).change(function () {
    changeHeight()
})

$(document).ready(function () {

    // $('.dropdown-submenu a.js-dropdown-submenu').on("click", function(e){
    //     if($('.js-dropdown-submenu + .dropdown-menu').hasClass('d-block')){
    //         $('.js-dropdown-submenu + .dropdown-menu').next('div').addClass('d-none')
    //     }
    //     $(this).next('div').toggle()
    //     e.stopPropagation()
    //     e.preventDefault()
    // });

    $('.dropdown-item > button').click(function (e) {
        e.stopPropagation()
        e.preventDefault()
    });

    $(".dropdown-item").hover(
        function () {
            $(this).addClass("inverse")
        },
        function () {
            $(this).removeClass("inverse")
        }
    );


    $('.dropdown-submenu > button').hover(
        function () {
            $('.dropdown-submenu > button').next('.dropdown-menu').removeClass('show');
            $(this).next('.dropdown-menu').addClass('show');
            $(this).next('.dropdown-menu').hover(
                function () {
                    $(this).addClass('show');
                },
                function () {
                    $(this).removeClass('show');
                }
            )
        },
        function () {
            $('.dropdown-submenu > button').next('.dropdown-menu').removeClass('show');
        }
    );

// warning if array is empty
    $('.js-no-data').html('no records found')
    $('td, .js-no-data').each(function () {
        if ($(this).html() == 'no records found') {
            $(this).addClass('alert alert-warning')
        }
    })
// Resize Header
    $('.show').removeClass("show");

// Session
    sessionInitialize()
    $('#cardButton').click(function () {
        // console.log('this user ', user, 'getCartSession ', getCartSession('cart'))

        // console.log(
        //
        //     'user ', user,
        //     '\nwindow.sessionStorage', window.sessionStorage,
        //     '\nlocalStorage', localStorage
        // );

        sessionInitialize()
    })


// Resize Windows
    changeHeight();
    $(window).bind('resizeEnd', function () {
        //console.log('change : ', $('body').height());
        changeHeight();
    })

    $(window).resize(function () {
        if (this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function () {
            $(this).trigger('resizeEnd');
        }, 100);

    });

//  StarRating management
    $(".js-starRating").each(function () {
        //console.log('starRating  ->', $(this));
        $(this).html(starRating($(this).text()));
    });

//   Choice of type saving image
    $(".js_image_saveTo").change(function () {
        const newClass = "fa-" + $(this).val();
        const oldClass = "fa-" + (($(this).val() === "folder") ? "database" : "folder");
        $("#saveTo i").removeClass(oldClass).addClass(newClass);
        //console.log($(this).val());
    });

//  Cart management
    // Ajax : Submit to pay
    $('#cartShopping').submit(function (e) {
        e.preventDefault()
        if ($('#cartShopping-userId').length > 0) {

            let url = $(this).attr("action")
            let flashMessages = $('#flashMessages')
            let data = $(this).serialize()
            let html = null
            let js = null
            let message = null
            let type = null
            let title = null;

            // console.log('data form ', data,
            //     '\ncart,', cart,
            //     '\nsessionCart,', sessionCart,
            //     '\nform,', $(this).find('input')
            // )

            flashMessages.html(
                '       <div class="d-flex justify-content-center my-0 py-0' +
                '           <div class="spinner-border" role="status">\n' +
                '               <span class="sr-only">Loading...</span>\n' +
                '           </div>\n' +
                '       </div>'
            )

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',

                success: function (result, xhr) {
                    // console.log('result : ', result,'\nxhr : ', xhr,'\ncart : ', cart,'\nsessionCart : ', sessionCart)
                    type = 'success'
                    // message = 'Success ' + xhr.status + ' : ' + xhr.statusText
                    html = result.data.html
                    js = result.data.js
                    title = result.data.title
                    $('#cardModalLong').modal('hide')
                    sessionInitialize()
                },

                error: function (xhr, result, status, error) {
                    type = 'error'
                    message = 'Error ' + xhr.status + ' : ' + xhr.statusText
                    html = xhr.responseJSON.data.html
                },

                complete: function (xhr, result) {
                    $("#blocBody").html(html)
                    if (message) {
                        flashMessages.html('<div class="alert alert-warning"> ' + message + ' </div>')
                    }
                    if (js) {
                        $('body').append('<script>\n' + js + '\n</script>')
                        //displayEvent()
                    }
                    $('h1').html(title)
                }
            });
        } else {
            alert('login to your account to continue')
            window.location.href = '/login';
        }
    })


    $('.js-add-to-cart').click(function () {
        if ($('#cardButton').hasClass('d-none')) {
            $('#cardButton').removeClass('d-none')
        }
        const id = $(this).attr('data-value');

        //console.log('$(this).parents(#game-id)', $(this).parents('#game-'+id).html())
        // console.log('$(this).parents(.game-info)', $(this).parents('.game-info').html())
        const gameName = $(this).parents('#game-' + id).find('.js-name small').html().replace('\n', '').trim()

        const price = parseFloat($(this).parents('#game-' + id).find('.js-price').attr('data-value'))

        let cartGame = cart['game-' + id]
            ? cart['game-' + id]
            : {
                'id': id,
                'name': gameName,
                'price': price,
                'nb': 0,
            }

        cartGame['nb'] += 1
        cart['game-' + id] = cartGame
        setCartSession('cart', cart);
        addToCartGame(id, cart['game-' + id]);
    })
});


// funsctions js

//  Cart management
function inputCartChange(id) {
    $("#input-" + id).change(function () {
        //console.log('#js-games-selected input', id);
        if ($("#input-" + id).val() < 1) {
            deleteCartGame(id)
        } else {
            updateCartGame(id)
        }
    })
}

function numberOfGamesInCart() {
    const numberOfGamesInCart = $('#js-games-selected > div').length;
    $('.js-nb-games').html(numberOfGamesInCart);
}

function updateTotalBuy(sum) {
    const total = parseFloat($('#js-total-cart').html()) + sum
    //console.log('total', total)
    $('#js-total-cart').html(total.toFixed(2))
}

function addToCartGame(id, order) {
    // console.log('addToCartGame('+id+') total', $('#js-total-cart').html() ,'\norder', order ,'\ncart[game-' + id+'][nb]', cart['game-' + id]['nb'])
    const divId = 'js-cart-game-' + id;
    const newP = $("#js-cart-game-" + id);
    if (newP.length) {
        newP.find('input').val(order['nb'])
    } else {
        $('#js-games-selected').append
        (
            '<div id="' + divId
            + '" class="row m-0 py-1">' +
            '<div class="col-5 m-0 p-0">' + order['name'] + '</div>' +
            '<div class="col-2 m-0 p-0"> : $<span id="js-sum-' + id + '"> - </span></div>' +
            '<div class="col-2 m-0 p-0">= ' + order['price'] + '</div> ' +
            '<div class="col-2 m-0 p-0"><i class="fas fa-times"></i> ' +
            '<input id="input-game-' + id + '" name="orders[' + id + ']" type="number" value="1" min="0" class="w-50 js-input-games-selected" />' +
            '</div>' +
            '<div class="col-1 m-0"><button class="js-cart-delete btn-danger mx-5" onclick="deleteCartGame(\'game-' + id + '\')">' +
            '<i class="fa fa-trash"></i></button>' +
            '</div>' +
            '</div>'
        )
        inputCartChange('game-' + id);

    }
    numberOfGamesInCart();

    const sum = Math.round(order['price'] * 100 * order['nb']) / 100
    $('#js-sum-' + id).html(sum.toFixed(2))
    updateTotalBuy(sum)

}


function updateCartGame(id) {
    //console.log(id, ' - cart ', cart)

    //console.log('cartGame :', cart[id])
    let sum = -Math.round(cart[id]['price'] * 100 * cart[id]['nb']) / 100
    updateTotalBuy(sum)
    cart[id]['nb'] = $('#input-' + id).val()
    sum = Math.round(cart[id]['price'] * 100 * cart[id]['nb']) / 100
    updateTotalBuy(sum)
    $('#js-' + id.replace('game', 'sum')).html(sum)
    setCartSession('cart', cart);
}


function deleteCartGame(id) {
    //console.log('deleteCartGame ', cart)
    //console.log('deleteCartGame ', id, cart[id])
    const sum = -Math.round(cart[id]['price'] * 100 * cart[id]['nb']) / 100
    updateTotalBuy(sum)
    delete cart[id];
    $('#js-cart-' + id).remove();

    //console.log('cart ', cart)
    numberOfGamesInCart();
    setCartSession('cart', cart);

}

function SaveCartGame() {
    console.log('SaveCartGame')
}

// Session

function sessionInitialize() {

    $('#js-total-cart').html(0)
    // console.log('sessionInitialize')

    if ($('#user').length) {
        user = $('#user').text()
    }
    // clear session if user logout
    if (sessionStorage.getItem('user') !== 'anon' && user !== sessionStorage.getItem('user')) {
        sessionStorage.clear();
        $('#cardButton').addClass('d-none')
    }

    if (!sessionStorage.getItem('user') || user !== sessionStorage.getItem('user')) {
        sessionStorage.setItem('user', user)
    }


    if (getCartSession('cart').length) {
        sessionCart = JSON.parse(getCartSession('cart'))
        $('#cardButton').removeClass('d-none')
    }


    if (sessionCart && sessionCart.length > 0) {
        sessionCart.forEach(function (value) {
            const id = value['id']
            cart['game-' + id] = value
            //console.log('session cart ', id, value)
            addToCartGame(id, cart['game-' + id]);
        })

        // console.log('session cart ', cart)
    }


}

function setCartSession(name, jsonObject) {
    const myObject_json = JSON.stringify(Object.values(jsonObject));
    sessionStorage.setItem(name, myObject_json);
    /**
     console.log(

     'set session jsonObject ', jsonObject,
     '\nmyObject_json', myObject_json,
     '\nsessionStorage', sessionStorage,
     )
     /**/

}

function getCartSession(name) {
    return sessionStorage.getItem(name) || [];
}


//nav bar Layout
function changeDisplayHeader() {
    displayHeader = !displayHeader;
    const displayHeaderClass = displayHeader ? 'fas fa-level-up-alt' : 'fas fa-level-down-alt';
    // $(".js-navHeader").css('display', displayHeader ? '' : 'none');
    $("#displayHeader >i+i").attr('class', displayHeaderClass);
    changeHeight()
}

// height block section
function changeHeight() {
    $("#content, body > section").css(
        'min-height',
        ($('html').height() - (1.1 * $('body > header').height() + $('body > footer').height())) + 'px');

    $("body,body >section").css('margin-bottom', (1.1 * $("body > footer").height()) + 'px')
        .css('margin-top', 1.1 * $("body > header").height() + 'px');

    //$("#navigationLeft").css('top', $("body > header").height() + 'px')


}


//  StarRating management
function starRating(value) {
    //console.log('starRating ->', value);

    //let htmlCode = (value) + ' : ';
    let htmlCode = '';
    if (value.trim() !== '') {
        for (let i = 1; i <= 5; i++) {
            if (i - .25 <= value) {

                htmlCode += '<i class="fas fa-star" style="color:goldenrod"></i>'
            } else if (i - .75 < value) {
                htmlCode += '<i class="fas fa-star-half-alt" style="color:goldenrod"></i>'
            } else {
                htmlCode += '<i class="far fa-star"  style="color:white"></i>'
            }
            //.log(i, '\t', value, '\n--', htmlCode);
        }
    } else {
        htmlCode += '<i class="fas fa-star noStarRating"></i>'
    }

    //console.log('starRating', htmlCode);
    return htmlCode
}

// Form : display or no input element
// className : entity name
// element   : property name
// add an html tag to the form with the class 'js-element'

function displayElement(className, element) {
    // console.log('displayElement(',className,', ',element,')')

    let Id = className + '_' + element;
    let divId = $('#' + className + '_' + element);
    let divClass = $('.js-' + element);

    const values = divClassMapValues(divClass);
    const valueDivId = divId.val()
    if (divId.val() && divId.val().length) {
        divClass.parent().removeClass('d-none');
    } else {
        divClass.parent().addClass('d-none');
    }
    //console.log('$(\'#', className, '-', element + '\').val()', divId.val());

    divId.change(function () {
        //console.log(Id, '\ndivId', divId.val())
        //console.log(element, ' : ', values)
        //console.log('$(#js-element).val()', divId.val());
        if (divId.val().length) {
            divClass.parent().removeClass('d-none');
            divId.prop('required', true);
        } else {
            divClass.parent().addClass('d-none');
            divClassOriginValues(divClass);
            divId.prop('required', false);
        }
    });
}

function divClassMapValues(divClass) {
    let vals = [];
    divClass.each(function () {
        vals[$(this).attr('id')] = $(this).val();
    })
    return vals;
}

function divClassOriginValues(divClass) {
    divClass.each(function () {
        $(this).val([$(this).attr('id')]);
        // console.log('divClassOriginValues val', $(this).val(), ' values :', [$(this).attr('id')]);
    })
}

// end js script

