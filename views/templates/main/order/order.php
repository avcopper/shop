<?
$rsa = new \System\RSA($this->user->private_key);

$last_name = !empty($this->user->last_name) ? $rsa->decrypt($this->user->last_name) : '';
$name = !empty($this->user->name) ? $rsa->decrypt($this->user->name) : '';
$second_name = !empty($this->user->second_name) ? $rsa->decrypt($this->user->second_name) : '';
?>
<div class="container">
    <div class="main-header">
        <div class="breadcrumbs">
            <a href="">Главная</a><span class="breadcrumbs-separator"></span>
            <span>Оформление заказа</span>
        </div>

        <h1>Оформление заказа</h1>
    </div>

    <? if (!empty($this->cart['items']) && is_array($this->cart['items'])): ?>
        <form action="/orders/finish/" method="post" class="catalog-container" id="order">
            <div class="main-section">
                <div class="order-item">
                    <a href="" class="order-item-title order-user">Покупатель</a>

                    <div class="order-item-container radio-container">
                        <div class="order-user-type <?= $this->user->id === '2' ? 'hidden' : '' ?>">
                            <label class="radio checked">
                                <input type="radio" name="type" value="1" data-target="order-user-physical" class="required" checked>
                                <span class="order-item-name">Физическое лицо</span>
                            </label>

                            <label class="radio">
                                <input type="radio" name="type" value="2" class="required" data-target="order-user-juridical">
                                <span class="order-item-name">Юридическое лицо</span>
                            </label>
                        </div>

                        <div class="order-item-slider order-user-physical">
                            <? if ($this->user->id !== '2'): ?>
                                <label for="p_profile">Профиль доставки</label>
                                <select name="p_profile" id="p_profile" class="required">
                                    <option value="0" selected>Новый профиль</option>
                                    <? if (!empty($this->profiles) && is_array($this->profiles)):
                                        foreach ($this->profiles as $profile):
                                            if ($profile->user_type_id !== '1') continue; ?>
                                            <option value="<?= $profile->id ?>"
                                                    data-user_type_id="<?= $profile->user_type_id ?>"
                                                    data-p_name="<?= ($rsa)->decrypt($profile->name) ?>"
                                                    data-p_email="<?= $profile->email ?>"
                                                    data-p_phone="<?= $profile->phone ?>"
                                                    data-index=""
                                                    data-city_id="<?= $profile->city_id ?>"
                                                    data-city="<?= $profile->city ?>"
                                                    data-street_id="<?= $profile->street_id ?>"
                                                    data-street="<?= $profile->street ?>"
                                                    data-house="<?= $profile->house ?>"
                                                    data-building="<?= $profile->building ?>"
                                                    data-flat="<?= $profile->flat ?>"
                                                    data-comment="<?= $profile->comment ?>"
                                            >
                                                <?= $profile->city . ', ' . $profile->street . ', ' . $profile->house . ($profile->flat ? ('-' . $profile->flat) : '') ?>
                                            </option>
                                        <? endforeach;
                                    endif; ?>
                                </select>
                                <div class="message_error"></div>
                            <? else: ?>
                                <input type="hidden" name="p_profile" value="0">
                            <? endif; ?>

                            <label for="p_name">Контактное лицо <span class="red">*</span></label>
                            <input type="text" name="p_name" id="p_name" value="<?= ($last_name . ' ' . $name . ' ' . $second_name) ?>" class="required">
                            <div class="message_error"></div>

                            <label for="p_email">E-mail <span class="red">*</span></label>
                            <input type="text" name="p_email" id="p_email" value="<?= $this->user->email ?? '' ?>" class="required">
                            <div class="message_error"></div>

                            <label for="p_phone">Телефон <span class="red">*</span></label>
                            <input type="text" name="p_phone" id="p_phone" value="<?= $this->user->phone ?? '' ?>" class="required">
                            <div class="message_error"></div>
                        </div>

                        <div class="order-item-slider order-user-juridical">
                            <label for="j_profile">Профиль доставки</label>
                            <select name="j_profile" id="j_profile" class="required">
                                <option value="0" selected>Новый профиль</option>
                                <? if (!empty($this->profiles) && is_array($this->profiles)):
                                    foreach ($this->profiles as $profile):
                                        if ($profile->user_type_id !== '2') continue; ?>
                                        <option value="<?= $profile->id ?>"
                                                data-user_type_id="<?= $profile->user_type_id ?>"
                                                data-j_name="<?= ($rsa)->decrypt($profile->name) ?>"
                                                data-j_email="<?= $profile->email ?>"
                                                data-j_phone="<?= $profile->phone ?>"
                                                data-index=""
                                                data-city_id="<?= $profile->city_id ?>"
                                                data-city="<?= $profile->city ?>"
                                                data-street_id="<?= $profile->street_id ?>"
                                                data-street="<?= $profile->street ?>"
                                                data-house="<?= $profile->house ?>"
                                                data-building="<?= $profile->building ?>"
                                                data-flat="<?= $profile->flat ?>"
                                                data-comment="<?= $profile->comment ?>"
                                                data-company='<?= $profile->company ?>'
                                                data-j_address="<?= $profile->address_legal ?>"
                                                data-inn="<?= $profile->inn ?>"
                                                data-kpp="<?= $profile->kpp ?>"
                                        >
                                            <?= $profile->company . ', ' . $profile->city . ', ' . $profile->street . ', ' . $profile->house ?>
                                        </option>
                                    <? endforeach;
                                endif; ?>
                            </select>
                            <div class="message_error"></div>

                            <label for="j_name">Контактное лицо <span class="red">*</span></label>
                            <input type="text" name="j_name" id="j_name" value="<?= ($last_name . ' ' . $name . ' ' . $second_name) ?>" class="required">
                            <div class="message_error"></div>

                            <label for="j_email">E-mail <span class="red">*</span></label>
                            <input type="text" name="j_email" id="j_email" value="<?= $this->user->email ?? '' ?>" class="required">
                            <div class="message_error"></div>

                            <label for="j_phone">Телефон <span class="red">*</span></label>
                            <input type="text" name="j_phone" id="j_phone" value="<?= $this->user->phone ?? '' ?>" class="required">
                            <div class="message_error"></div>

                            <label for="company">Название компании <span class="red">*</span></label>
                            <input type="text" name="company" id="company" class="required">
                            <div class="message_error"></div>

                            <label for="j_address">Юридический адрес <span class="red">*</span></label>
                            <input type="text" name="j_address" id="j_address" class="required">
                            <div class="message_error"></div>

                            <label for="inn">ИНН <span class="red">*</span></label>
                            <input type="text" name="inn" id="inn" class="required">
                            <div class="message_error"></div>

                            <label for="kpp">КПП</label>
                            <input type="text" name="kpp" id="kpp">
                            <div class="message_error"></div>
                        </div>
                    </div>
                </div>

                <div class="order-item">
                    <a href="" class="order-item-title order-delivery">Доставка</a>

                    <div class="order-item-container">
                        <? if (!empty($this->deliveries) && is_array($this->deliveries)): ?>
                            <? foreach ($this->deliveries as $delivery): ?>
                                <div class="radio-container">
                                    <label class="radio <?= $delivery->id === '1' ? 'checked' : '' ?>">
                                        <input type="radio" name="delivery" value="<?= $delivery->id ?>" <?= $delivery->id === '1' ? 'checked' : '' ?>>
                                        <span class="order-item-name"><?= $delivery->name ?></span>
                                        <span class="order-item-price">
                                        Стоимость:
                                        <? if (!empty($delivery->price)): ?>
                                            <span><?= $delivery->price ?></span> р.
                                        <? elseif (!empty($delivery->price_from) || !empty($delivery->price_to)): ?>
                                            <span>
                                                <?=
                                                ($delivery->price_from ? ('от ' . $delivery->price_from . ' ') : '') .
                                                ($delivery->price_to ? ('до ' . $delivery->price_to) : '')
                                                ?>
                                            </span> р.
                                        <? else: ?>
                                            бесплатно
                                        <? endif; ?>
                                    </span>
                                        <span class="order-item-time">Срок доставки: <span><?= $delivery->time ?></span></span>
                                        <span class="order-item-desc"><?= $delivery->description ?></span>
                                    </label>
                                </div>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>

                <div class="order-item order-item-delivery hidden">
                    <a href="" class="order-item-title order-region">Адрес доставки</a>

                    <div class="order-item-container">
                        <div class="relative visible">
                            <label for="delivery_city">Населенный пункт <span class="red">*</span></label>
                            <input type="hidden" name="city_id" value="<?= $this->location->id ?? '' ?>" class="required">
                            <input type="text" name="city" id="delivery_city" class="required"
                                   value="<?= $this->location->id ? ($this->location->region . ', ' . $this->location->city . ' ' . $this->location->shortname) : '' ?>">
                            <ul class="order-item-search-result"></ul>
                            <div class="message_error"></div>
                        </div>

                        <div class="relative visible">
                            <label for="delivery_street">Улица <span class="red">*</span></label>
                            <input type="hidden" name="street_id" class="required">
                            <input type="text" name="street" id="delivery_street" class="required">
                            <ul class="order-item-search-result"></ul>
                            <div class="message_error"></div>
                        </div>

                        <div class="order-delivery-address">
                            <div class="order-delivery-house">
                                <label for="delivery_house">Дом <span class="red">*</span></label>
                                <input type="text" name="house" id="delivery_house" class="required">
                                <div class="message_error"></div>
                            </div>

                            <div class="order-delivery-building">
                                <label for="delivery_building">Корпус</label>
                                <input type="text" name="building" id="delivery_building">
                            </div>

                            <div class="order-delivery-flat">
                                <label for="delivery_flat">Квартира</label>
                                <input type="text" name="flat" id="delivery_flat">
                            </div>
                        </div>

                        <label for="delivery_comment">Комментарий к заказу</label>
                        <textarea name="comment" id="delivery_comment"></textarea>

                        <div class="order-item-comment">
                            Выберите профиль доставки или введите свой город и адрес.
                        </div>
                    </div>
                </div>

                <div class="order-item">
                    <a href="" class="order-item-title order-payment">Оплата</a>

                    <div class="order-item-container radio-container">
                        <? if (!empty($this->payments) && is_array($this->payments)): ?>
                            <? foreach ($this->payments as $payment): ?>
                                <label class="radio <?= $payment->id === '1' ? 'checked' : '' ?>">
                                    <input type="radio" name="payment" value="<?= $payment->id ?>" <?= $payment->id === '1' ? 'checked' : '' ?>>
                                    <span class="order-item-name"><?= $payment->name ?></span>
                                </label>
                            <? endforeach; ?>
                        <? endif; ?>
                    </div>
                </div>

                <div class="order-item">
                    <a href="" class="order-item-title order-items">Заказ</a>

                    <div class="order-item-container order-product-container">
                        <div class="order-product-header">
                            <div class="order-product-cell order-product-image"></div>
                            <div class="order-product-cell order-product-title">Наименование</div>
                            <div class="order-product-cell order-product-weight">Вес</div>
                            <div class="order-product-cell order-product-dicsount">Скидка</div>
                            <div class="order-product-cell order-product-values">Цена</div>
                            <div class="order-product-cell order-product-count">Кол-во</div>
                            <div class="order-product-cell order-product-totalvalues">Сумма</div>
                        </div>

                        <? foreach ($this->cart['items'] as $cart_item): ?>
                            <div class="order-product">
                                <div class="order-product-desc">
                                    <div class="order-product-cell order-product-image">
                                        <a href=""><img src="/uploads/catalog/<?= $cart_item->product_id ?>/<?= $cart_item->preview_image ?>" alt=""></a>
                                    </div>
                                    <div class="order-product-cell order-product-title">
                                        <a href=""><?= $cart_item->name ?></a>
                                        <span>Производитель <?= $cart_item->vendor ?></span>
                                    </div>
                                    <div class="order-product-cell order-product-weight">
                                        <i>Вес</i>
                                        0 кг
                                    </div>
                                    <div class="order-product-cell order-product-dicsount">
                                        <i>Скидка</i>
                                        <?= $cart_item->discount ?>%
                                    </div>
                                </div>
                                <div class="order-product-val">
                                    <div class="order-product-cell order-product-values">
                                        <i>Цена</i>
                                        <div class="order-product-price">
                                            <?= $cart_item->discount_price ?? $cart_item->price ?> <?= $cart_item->currency ?>
                                        </div>
                                        <? if (!empty($cart_item->discount_price)): ?>
                                            <span class="order-product-oldprice"><?= $cart_item->price ?> <?= $cart_item->currency ?></span>
                                        <? endif; ?>
                                    </div>
                                    <div class="order-product-cell order-product-totalcount">
                                        <i>Кол-во</i>
                                        <div class="order-product-count"><?= $cart_item->count ?> <?= $cart_item->unit ?></div>
                                    </div>
                                    <div class="order-product-cell order-product-totalvalues">
                                        <i>Сумма</i>
                                        <div class="order-product-total">
                                            <?= $cart_item->discount_sum ?? $cart_item->sum ?> <?= $cart_item->currency ?>
                                        </div>
                                        <? if (!empty($cart_item->discount_sum)): ?>
                                            <div class="order-product-oldtotal"><?= $cart_item->sum ?> <?= $cart_item->currency ?></div>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        <? endforeach;?>
                    </div>
                </div>

                <div class="personal-data">
                    <label class="checkbox checked">
                        <input type="checkbox">
                        Я согласен на <a href="">обработку персональных данных</a> *
                    </label>
                </div>

                <div class="order-submit">
                    <button type="submit" class="btn">Оформить заказ</button>
                </div>
            </div>

            <div class="rightmenu">
                <div class="side-order">
                    <div class="side-order-header">
                        <div class="side-order-container">
                            <div class="side-order-left">Ваш заказ</div>
                            <div class="side-order-right"><a href="">Изменить</a></div>
                        </div>
                    </div>
                    <div class="side-order-body">
                        <div class="side-order-container">
                            <div class="side-order-left">Товаров:</div>
                            <div class="side-order-right side-order-count"><?= $this->cart['count_items'] ?> шт.</div>
                        </div>
                        <div class="side-order-container">
                            <div class="side-order-left">На сумму:</div>
                            <div class="side-order-right side-order-price"><?= $this->cart['discount_sum'] ?? $this->cart['sum'] ?> р.</div>
                        </div>
                        <? if (!empty($this->cart['discount_sum'])): ?>
                            <div class="side-order-container">
                                <div class="side-order-left"></div>
                                <div class="side-order-right "><div class="side-order-oldprice"><?= $this->cart['sum'] ?> р.</div></div>
                            </div>
                        <? endif; ?>
                        <div class="side-order-container">
                            <div class="side-order-left">Доставка:</div>
                            <div class="side-order-right side-order-delivery">бесплатно</div>
                        </div>
                    </div>
                    <div class="side-order-footer">
                        <div class="side-order-container">
                            <div class="side-order-left">Итого:</div>
                            <div class="side-order-right side-order-total"><?= $this->cart['discount_sum'] ?? $this->cart['sum'] ?> р.</div>
                        </div>
                        <button type="submit" class="btn">Оформить заказ</button>
                    </div>
                </div>
            </div>
        </form>
    <? else: ?>
        <div class="basket-empty">
            <p>Ваша корзина пуста</p>
            <p>Нажмите <a href="/catalog/">здесь</a>, чтобы продолжить покупки</p>
        </div>
    <? endif; ?>
</div>
