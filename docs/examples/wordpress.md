---
title: Wordpress example
permalink: /examples/wordpress
---

> :bulb: The **prerequisite** of this example is already installed library and required `vendor/autoload.php`.
> See installation in the [front page]({{site.github.url}}) and Composer tutorial if needed.

To integrate responsive pagination into Wordpress, the base Paginator class must be overridden a little bit.

## Create a new file and override Paginator

Create a new file in `wp-content/themes/__theme__/inc/WPResponsivePaginator.php` and place the code below.

```php
class WPResponsivePaginator extends \ResponsivePagination\Paginator
{
    public function formatLink(int $page): string
    {
        return get_pagenum_link($page);
    }
}
```

## Add code to functions.php

Insert following code into theme's `functions.php` file or another new file which will be then included in `functions.php`.

```php
require 'inc/WPResponsivePaginator.php';

function print_paginator(){
    global $wp_query;
    global $paged;

    $currentPage = (empty($paged)) ? 1 : $paged;
    $totalPages = $wp_query->max_num_pages ?: 1;

    $paginator = (
        new \ResponsivePagination\Paginator(
            $currentPage,
            $totalPages,
            4 // The initial range
        )
    )->addBreakpoint(3, 'd-none d-lg-block', 'd-lg-none')
    ->addBreakpoint(2, 'd-none d-md-block', 'd-md-none')
    ->addBreakpoint(1, 'd-none d-sm-block', 'd-sm-none')
    ->generate();

    ?>
    <nav class="pagination">
        <div class="arrow backward">
            <?php if($paginator->prev === null): ?>
                Previous articles
            <?php else: ?>
                <a href="<?php echo $paginator->prev->link ?>" rel="prev">Previous articles</a>
            <?php endif; ?>
        </div>
        <div class="arrow forward">
            <?php if($paginator->next === null): ?>
                Next articles
            <?php else: ?>
                <a href="<?php echo $paginator->next->link ?>" rel="next">Next articles</a>
            <?php endif; ?>
        </div>
        <div class="pages">
            <?php foreach ($paginator as $button): ?>
                <?php if ($button->dots): ?>
                    <span class="dots <?php echo $button->className ?>">...</span>
                <?php elseif ($button->active): ?>
                    <span class="active"><?php echo $button->page ?></span>
                <?php else: ?>
                    <a href="<?php echo $button->link ?>" class="<?php echo $button->className ?>"><?php echo $button->page ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </nav>
    <?php
}
```

## Archive template file

```php
if (have_posts()):
    while (have_posts()):
        the_post();
        // Template content
    endwhile;

    print_paginator();
endif;
```
