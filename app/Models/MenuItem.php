<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory;
    use softDeletes;
    protected $table = "menu_items";
    protected $fillable = ["menu_header_id","parent_id","user_id","name","short_name","icon","main_route","route","priority"];
    protected $appends = ['all_actions','action_route'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class,"user_id");
    }
    public function menu_header(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuHeader::class,"menu_header_id");
    }
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItem::class,"parent_id");
    }
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuItem::class,"parent_id");
    }
    public function actions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MenuAction::class,"item_action","menu_item_id","menu_action_id");
    }
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class,"role_menu","menu_item_id","role_id");
    }
    public function getActionRouteAttribute(): string
    {
        return $this->route .".". $this->main_route;
    }
    public function getAllActionsAttribute(): string
    {
        return $this->route .".*";
    }
}
