<section class="content-header">
  <h1>
    {{ isset($pageTitle) ? $pageTitle : 'Page Title' }}
    @if(isset($otherLinks['link']))
    	<small><a href="{{ $otherLinks['link'] }}">{{ $otherLinks['text'] }}</a></small>
    @endif
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>