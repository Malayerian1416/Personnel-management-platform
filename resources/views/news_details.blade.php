@extends('layouts.landing')
@section('content')
    <section class="container mt-3">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb mb-0 rad25">
                        <li class="breadcrumb-item"><a href="{{route("Home")}}">صفحه اصلی</a></li>
                        <li class="breadcrumb-item"><a href="#">اخبار</a></li>
                        <li class="breadcrumb-item active" aria-current="page">جزئیات اخبار</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>
    <section class="container-fluid mt-3 pb-5">
        <div class="container px-5 py-3 box  bg-page">
            <div class="row">
                <div class="col-lg-12">
                    <div id="blog_details">
                        <h1 class="pt-3 IRANSansWeb_Medium">{{$article->title}}</h1>
                        <div class="mb-4">
                            <span class="pl-2"><i class="fas fa-history pl-1"></i>{{"تاریخ ارسال : ".verta($article->created_at)->format("Y/m/d")}}</span>
                        </div>
                        <img alt="{{$article->title}}" src="{{asset("storage/news/$article->id/$article->image")}}" class="img-fluid rad25 pb-3 w-75" />
                        <div>
                            {!! $article->description !!}
                        </div>
                    </div>
                </div>
            </div>
            @if(count($files) > 1)
                <h5 class="mt-3">گالری تصاویر</h5>
                <div id="owl-Gallery" class="owl-carousel">
                    @forelse($files as $file)
                        <div class="p-2">
                            <img class="img-fluid border border-danger" alt="{{$article->title}}" src="{{asset("storage/news/$file")}}" onclick="transferImage(this.src)" data-toggle="modal" data-target="#gallery">
                        </div>
                    @empty
                    @endforelse
                </div>
            @endif
        </div>
    </section>
    <div class="modal fade p-0" id="gallery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body bg-transparent">
                    <img id="largeImage" alt="{{$article->title}}" style="object-fit: cover" src="{{asset("storage/news/$article->id/$article->image")}}" class="img-fluid m-auto">
                </div>
            </div>
        </div>
    </div>
@endsection
