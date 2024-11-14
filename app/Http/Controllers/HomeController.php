<?php

namespace App\Http\Controllers;

use App\Models\CompanyInformation;
use App\Models\News;
use App\Models\Occasion;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        Visit::SaveVisit($request->ip());
        $agent = new Agent();
        $agent->isMobile() || $agent->isTablet() ? $paginate = 1 : $paginate = 4;
        $news = News::query()->where("published",1)->orderBy("id","desc")->paginate($paginate);
        $occasions = Occasion::query()->where("publish",1)->orderBy("updated_at","desc")->get();
        return view('home',["occasions" => $occasions, "news" => $news, "company" => CompanyInformation::query()->with("ceo")->first()]);
    }
    public function NewsDetails($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $article = News::query()->where("published",1)->findOrFail($id);
        $article->update(["views" => ($article->views ?: 0) + 1]);
        $files = Storage::disk("news")->allFiles($article->id);
        return view("news_details", ["article" => $article, "files" => $files]);
    }
    public function ContactUs(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("contact_us",["company" => CompanyInformation::query()->first()]);
    }
    public function AboutUs(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view("about_us",["company" => CompanyInformation::query()->first()]);
    }
}

