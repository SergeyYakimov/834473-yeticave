<nav class="nav">
    <ul class="nav__list container">
        <?php foreach($categories as $value):?>
        <li class="nav__item">
            <a href="all-lots.html"><?=$value['name'];?></a>
        </li>
        <?php endforeach;?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?=$error['name']; ?></h2>
    <p><?=$error['message']; ?></p>
</section>
