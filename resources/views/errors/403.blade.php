@extends('errors::minimal')

@include('includes.header')

<section class="error-403">
   <div class="container">
   <div class="fadeInDown text-center">
      <!-- <img src="images/403-error.png"> -->
      <img src="<?= url('/imgs/error-page/403-error.png') ?>">
      <h3 class="sub-title-error">FORBIDDEN ERROR</h3>
      <p class="error-disc">You don't have permission to access on this server</p>
   </div>
   </div>
</section>
@include('includes.footer')

