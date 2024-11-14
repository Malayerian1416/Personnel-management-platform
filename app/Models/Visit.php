<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;
    protected $table = "visits";
    protected $fillable = ["ip"];

    public static function SaveVisit($ip): void
    {
        self::query()->create(["ip" => $ip]);
    }
    public static function statistics(): array
    {
        $result = [];
        for($i = 6; $i >= 0; $i--){
            $date = verta()->subMonths($i);
            $last_month_day = $date->daysInMonth;
            $year = $date->year;
            $month = $date->month;
            $from_date = gmdate("Y/m/d H:i:s",Verta::createJalali($year,$month,1,0,0,0)->timestamp);
            $to_date = gmdate("Y/m/d H:i:s",Verta::createJalali($year,$month,$last_month_day,0,0,0)->timestamp);
            $month_name = $date->format("F");
            $result[] = [
                "month" => $month_name,
                "count" => self::query()->whereDate("created_at",">=",$from_date)->whereDate("created_at","<=",$to_date)->count()
            ];
        }
        return $result;
    }
}
