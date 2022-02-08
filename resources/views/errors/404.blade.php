@extends('errors::minimal')

@include('includes.header')

<section class="error-404">
   <div class="container">
      <div class="fadeInDown text-center">
      <h2 class="title-error">ERROR</h2>
      <!-- <img src="images/404-error.png"> -->
      <img src="<?= url('/imgs/error-page/404-error.png'); ?> ">
      <h3 class="sub-title-error">OOPS! PAGE NOT FOUND</h3>
      <p class="error-disc">The page you are trying to access does not exist.</p>
      <p class="error-disc last-disc">Try going back to our homepage.</p>
      <a href="<?= url('/') ?>" class="btn btn-primary">GO TO HOMEPAGE</a>
      </div>
   </div>
</section>
@include('includes.footer')

