{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<section class="sub-container" wow fadeInUp" data-wow-delay="0.6s">
  <div class="container">
    <table><tr><td>
      <!-- Start plan content  -->            
      <h1 class="heading">{lang('terms_&_conditions')}</h1>       
      {if isset($content['terms'])}
        <p>{$content['terms']}</p>
      {/if}
  <!-- Close plan content  -->
  </td></tr></table>
</div>
</section>

{/block}