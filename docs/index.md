---
title: Responsive pagination
---

[![PHP from Packagist](https://img.shields.io/packagist/php-v/arxeiss/responsive-pagination)](https://packagist.org/packages/arxeiss/responsive-pagination)
[![Packagist Version](https://img.shields.io/packagist/v/arxeiss/responsive-pagination)](https://packagist.org/packages/arxeiss/responsive-pagination)
[![Travis (.com)](https://img.shields.io/travis/com/arxeiss/responsive-pagination?label=Travis)](https://travis-ci.com/arxeiss/responsive-pagination)
[![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/arxeiss/responsive-pagination?logo=code-climate)](https://codeclimate.com/github/arxeiss/responsive-pagination)
[![Code Climate coverage](https://img.shields.io/codeclimate/coverage/arxeiss/responsive-pagination?logo=code-climate)](https://codeclimate.com/github/arxeiss/responsive-pagination)
![GitHub](https://img.shields.io/github/license/arxeiss/responsive-pagination)
[![PHP STAN](https://img.shields.io/badge/phpstan-level%208-informational)](https://github.com/phpstan/phpstan)

# About

A Responsive pagination library can generate pagination like many others. But the main goal is to provide **responsive functionality** for hiding and showing some link buttons according to breakpoints.

Responsivity is fully controlled only by your own **CSS classes**, so can be used with Bootstrap, any other framework or custom solution.<br>
It also can be used with any CMS. There is an example with [Wordpress too](examples/wordpress).

<p align="center">
<a href="https://github.com/arxeiss/responsive-pagination/blob/master/docs/responzive-pagination-screen.png?raw=true"><img src="https://github.com/arxeiss/responsive-pagination/blob/master/docs/responzive-pagination-screen.png?raw=true" alt="Responsive pagination screenshot" width="300"></a><br>
<em>Click to see full size</em>
</p>

Live usage is available also on my blog [kutac.cz/blog](https://www.kutac.cz/blog?strana=7) *(in Czech language)*.

---

## Installation

```bash
composer require arxeiss/responsive-pagination
```

## Usage and examples

- [Basic example with Bootstrap integration and searching](examples/basic)
- [Integration into Wordpress](examples/wordpress)
- [Live demo on my blog](https://www.kutac.cz/blog?strana=7)

## Range explanation

The `range` argument specifies how many button links around the actual page button are shown to **each** side.
But it is not the final amount of buttons, because first and last page buttons are not counted in as well as dots.

When the current page is first or last, the range is increased by 2. If the current page is second or second from the end, the range is increased by 1.
The reason is simply to show more buttons when there is a space.

### Range vs max visible items
There is a direct proportion between the `range` argument and the maximum amount of visible buttons. The easy equation is `max buttons = range * 2 + 5`.
If you pass range into constructor or addBreapoint method, you can calculate how many buttons will be maximally visible.
There is also a static method `rangeToMaxVisible` which does the same calculation.

**Equation explanation**:<br>
- `range * 2` - there are `range` amount of buttons to each side from the current page button
- `+1` - the current page button
- `+2` - the first and the last page button
- `+2` - dots after the first and before the last page button
