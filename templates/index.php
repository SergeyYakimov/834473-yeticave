<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <?php foreach($categories as $value):?>
        <li class="promo__item promo__item--<?=$value['alias'];?>">
            <a class="promo__link" href="pages/all-lots.html"><?=$value['name'];?></a>
        </li>
        <?php endforeach;?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach($lots_list as $key => $value):?>
        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?=$value['image'];?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
                <span class="lot__category"><?=$value['name'];?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php/?id=<?=$value['lot_id']; ?>"><?=htmlspecialchars($value['title']);?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=format_price($value['start_price']);?></span>
                    </div>
                    <div class="lot__timer timer">
                        <?=get_time_till_closing_time($value['completion_date']); ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
</section>
