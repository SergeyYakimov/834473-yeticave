<section class="rates container">
    <h2>Мои ставки</h2>
    <?php if (empty($rates)): ?>
    <p>У Вас нет ставок.</p>
    <?php else: ?>
    <table class="rates__list">
        <?php foreach ($rates as $rate): ?>
        <tr class="rates__item <?=$rate['is_win'] ? 'rates__item--win' : ($rate['is_end'] ? 'rates__item--end' : '');?>">
                <td class="rates__info">
                <div class="rates__img">
                    <img src="<?=$rate['image'];?>" width="54" height="40" alt="<?=htmlspecialchars($rate['category']);?>">
                </div>
                <?php if ($rate['winner_id'] === $user['user_id']): ?>
                <div>
                    <h3 class="rates__title"><a href="lot.php?id=<?=$rate['lot_id']; ?>"><?=htmlspecialchars($rate['lot_title']); ?></a></h3>
                    <p><?=htmlspecialchars($rate['contacts']); ?></p>
                </div>
                <?php else: ?>
                <h3 class="rates__title"><a href="lot.php?id=<?=$rate['lot_id']; ?>"><?=htmlspecialchars($rate['lot_title']); ?></a></h3>
                <?php endif; ?>
            </td>
            <td class="rates__category">
                <?=$rate['category']; ?>
            </td>
            <td class="rates__timer">
                <div class="timer <?=$rate['is_finishing'] ? 'timer--finishing' : '';?> <?=$rate['is_end'] ? 'timer--end' : '';?> <?=$rate['is_win'] ? 'timer--win' : '';?>">
                    <?=$rate['is_win'] ? 'Ставка выиграла' : ($rate['is_end'] ? 'Торги окончены' : get_time_of_end_lot($rate['lot_completion_date'])); ?>
                </div>
            </td>
            <td class="rates__price">
                <?=number_format($rate['rate'], 0, '.', ' '); ?> р
            </td>
            <td class="rates__time">
                <?=get_rate_add_time($rate['date_add']); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</section>
