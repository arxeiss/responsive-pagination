---
title: Basic example with searching
permalink: /examples/basic
---

> :bulb: The **prerequisite** of this example is already installed library, see installation in the [front page]({{site.github.url}}).

The library can be used in MVC architecture system as well in single PHP script. Also can be change to any template system.

## Controller

```php
$totalItems = $blogPosts->count(); // Get total amount of items
$currentPage = (int)max($request->query('page'), 1); // Get current page, use $_GET['page'] directly or whatever
$totalPages = (int)ceil($totalItems / $itemsPerPage); // Count total amount of pages

$search = $request->query('search'); // If should support search, get searched query
if ($search) {
    $search = urlencode($search); // URLencode value to be safe when putting into URL
}

// Code above update according to your framework
$paginator = (
        new \ResponsivePagination\Paginator(
            $currentPage,
            $totalPages,
            4, // The initial range
            $search ? "?search={$search}&page=%%page%%" : "?page=%%page%%", // %%page%% is placeholder
            $search ? "?search={$search}" : '/'
        )
    )->addBreakpoint(3, 'd-none d-lg-block', 'd-lg-none')
    ->addBreakpoint(2, 'd-none d-md-block', 'd-md-none')
    ->addBreakpoint(1, 'd-none d-sm-block', 'd-sm-none')
    ->generate();
```

## Template

```html
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
```
