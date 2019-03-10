<section class="lot-item container">
    <h2><?=$lot['title']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="../<?=$lot['image']; ?>" width="730" height="548" alt="">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['name']; ?></span></p>
          <p class="lot-item__description"><?=$lot['description']; ?></p>
        </div>
        <div class="lot-item__right">
          <?php if (!empty($user)): ?>
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
                <?=get_time_till_closing_time($lot['completion_date']); ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=format_price($lot['price']); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=format_price($lot['step_rate'] + $lot['price']); ?></span>
              </div>
            </div>
            <?php if ($show_add_rate_form): ?>
                <form class="lot-item__form" action="lot.php?id=<?=$lot['lot_id']; ?>" method="post">
                    <p class="lot-item__form-item form__item<?=!isset($errors['cost']) ? '' : ' form__item--invalid'; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="<?=$lot['price'] + $lot['step_rate']; ?>" required<?=empty($information['cost']) ? '' : ' value="' . $information['cost'] . '"'; ?>>
                        <span class="form__error"><?=!isset($errors['cost']) ? '' : $errors['cost']; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок (<span><?=count($rates); ?></span>)</h3>
                <?php if (!empty($rates)): ?>
                <table class="history__list">
                    <?php foreach ($rates as $rate): ?>
                    <tr class="history__item">
                        <td class="history__name"><?=htmlspecialchars($rate['user']); ?></td>
                        <td class="history__price"><?=$rate['rate']; ?>р.</td>
                        <td class="history__time"><?=get_rate_add_time($rate['date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
          </div>
          <?php endif;?>
        </div>
      </div>
</section>
