/* установка ширины подменю левого меню каталога */
function setLeftSubMenuWidth() {
    let width = $('.catalog-main').width();
    $('.catalog-leftsubmenu').css('width', width);
}

/* убирает уведомление с экрана */
function removeNotification() {
    setTimeout(function () {
        $('#notification').removeClass('active');
    }, 3000);
}

/* проверка состояния чекбоксов в фильтре каталога при загрузке и установка их состояния */
function setCheckboxCondition(elem) {
    elem.each(function () {
        let span = $(this).next().find('span');
        $(this).prop('checked') ? span.addClass('checked') : span.removeClass('checked');
    });
}

/* проверка состояния фильтров в каталоге - свернутое/развернутое */
function checkFilterState() {
    $('.catalog-left-filter-title').each(function () {
        if ($(this).hasClass('active')) $(this).next().show();
    });
}

/* проверяет пользовательские данные */
function checkUserData(value, type, count = 0) {
    let numbers = new RegExp('^[0-9]' +
        ((0 === count) ?
            '+' :
            ('{0,' + count + '}')) +
        '$','i'), // цифры, произвольное количество или ограниченное от нуля до count
        numbers_strict = new RegExp('^\\d{' + count + '}$','i'),    // цифры, строгое количество
        rus = new RegExp('^[а-яё\\- ".,]' +
            ((0 === count) ?
                '+' :
                ('{0,' + count + '}')) +
            '$','i'), // русские буквы, тире, пробел
        eng = new RegExp('^[a-z- ]' +
            ((0 === count) ?
                '+' :
                ('{0,' + count + '}')) +
            '$','i'), // английские буквы, тире, пробел
        rus_num = /^[0-9а-яё\- ".,]+$/miu, // русские буквы, цифры, тире и пробел
        rus_eng = /^[a-zа-яё\- ".,]+$/miu, // русские/английские буквы, тире и пробел
        date = /^(0?[1-9]|[12][0-9]|3[01])\.(0?[1-9]|1[012])\.((19|20)\d\d)$/i, // дата
        //pass = /((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{6,20})/g, // пароль
        pass = /((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20})/g, // пароль
        email = /^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,6}$/i, // email
        phone = /^\+?[78]?[ -]?[(]?9\d{2}\)?[ -]?\d{3}-?\d{2}-?\d{2}$/i; // телефон

    if (type === 'numbers') return numbers.test(value);
    if (type === 'numbers_strict') return numbers_strict.test(value);
    if (type === 'rus') return rus.test(value);
    if (type === 'eng') return eng.test(value);
    if (type === 'rus_eng') return rus_eng.test(value);
    if (type === 'rus_num') return rus_num.test(value);
    if (type === 'date') return date.test(value);
    if (type === 'pass') return pass.test(value);
    if (type === 'email') return email.test(value);
    if (type === 'phone') return phone.test(value);
    return false;
}

/* пересчитывает корзину при изменении количества товаров в ней */
function recalcProduct(product_id, count, elem) {
    $.ajax({
        method: "POST",
        dataType: 'json',
        url: "/cart/recalc/",
        data: {
            id: product_id,
            count: count
        },
        beforeSend: function() {
            $('#loader').show();
        },
        success: function(data){console.log(data);
            $('#loader').hide();

            if (!data.result) {
                $('#notification').html(data.data.message).addClass('active');
                removeNotification();
            } else {
                // цена за единицу
                elem.find('.basket-item-price span').html(data.data.item_discount_price ? data.data.item_discount_price : data.data.item_price);
                elem.find('.basket-item-oldprice span').html(data.data.item_discount_price ? data.data.item_price : '');
                // сумма
                elem.find('.basket-item-totalprice span').html(data.data.item_discount_sum ? data.data.item_discount_sum : data.data.item_sum);
                elem.find('.basket-item-oldtotalprice span').html(data.data.item_discount_sum ? data.data.item_sum : '');
                // экономия
                elem.find('.basket-item-economy span').html(data.data.item_sum_economy ? data.data.item_sum_economy : '');

                // сумма корзины
                $('.basket-order-price span').html(data.data.cart_discount_sum ? data.data.cart_discount_sum : data.data.cart_sum);
                $('.basket-order-oldprice span').html(data.data.cart_discount_sum ? data.data.cart_sum : '');
                // экономия корзины
                $('.basket-order-economy span').html(data.data.cart_economy ? data.data.cart_economy : '');
                // НДС корзины
                $('.basket-order-nds span').html(data.data.cart_discount_sum_nds ? data.data.cart_discount_sum_nds : (data.data.cart_sum_nds ? data.data.cart_sum_nds : ''));
                // количество в корзине
                $('.basket-order-total-count').html(data.data.count);

                // сообщение
                if (data.message) {
                    $('#notification').html(data.message).addClass('active');
                    removeNotification();
                }
                //data.message ? $('.basket-message').html(data.message).addClass('block') : $('.basket-message').html('').removeClass('block');
            }
        }
    });
}
