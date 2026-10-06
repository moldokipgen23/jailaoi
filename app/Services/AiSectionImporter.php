<?php
namespace App\Services;

use App\Models\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AiSectionImporter
{
    public function replace(int $userId, string $content, int $expectedCount): bool
    {
        $content = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($content));
        $data = json_decode($content, true);
        if ($userId < 1 || !is_array($data) || count($data) !== 1 || !isset($data[0]['sections']) || !is_array($data[0]['sections']) || (int) ($data[0]['uid'] ?? 0) !== $userId) return false;
        $sections = $data[0]['sections'];
        if (count($sections) !== $expectedCount || $expectedCount < 1 || $expectedCount > 20) return false;
        $rows = [];
        foreach ($sections as $section) {
            if (!is_array($section)) return false;
            if ((int) ($section['tp'] ?? 0) === 3) $section['tp'] = 8;
            $validator = Validator::make($section, [
                't'=>'required|string|max:255', 'st'=>'nullable|string|max:255', 'tp'=>'required|integer|in:1,2,8',
                'aid'=>'required|integer|min:0', 'cid'=>'required|integer|min:0', 'lid'=>'required|integer|min:0', 'cty'=>'required|integer|min:0',
                'noc'=>'required|integer|min:4|max:20', 'sl'=>'required|in:landscape,square,small_square',
            ]);
            if ($validator->fails()) return false;
            if ((int) $section['tp'] !== 1 && (int) $section['cty'] !== 0) return false;
            if (count(array_filter([$section['aid'], $section['cid'], $section['lid'], $section['cty']])) > 2) return false;
            foreach (['aid'=>'tbl_artist','cid'=>'tbl_category','lid'=>'tbl_language','cty'=>'tbl_city'] as $key=>$table) {
                if ($section[$key] > 0 && !DB::table($table)->where('id', $section[$key])->exists()) return false;
            }
            $rows[] = [
                'user_id'=>$userId, 'section_type'=>1, 'title'=>$section['t'], 'sub_title'=>$section['st'] ?? '',
                'type'=>$section['tp'], 'artist_id'=>$section['aid'], 'category_id'=>$section['cid'], 'language_id'=>$section['lid'], 'city_id'=>$section['cty'],
                'screen_layout'=>$section['sl'], 'no_of_content'=>$section['noc'], 'is_premium'=>0, 'order_by_upload'=>1, 'order_by_play'=>1,
                'is_paid'=>0, 'is_title'=>1, 'is_category'=>1, 'is_artist_name'=>1, 'view_all'=>1, 'sortable'=>0, 'status'=>1,
                'created_at'=>now(), 'updated_at'=>now(),
            ];
        }
        DB::transaction(function () use ($userId, $rows) {
            Section::where('user_id', $userId)->where('section_type', 1)->delete();
            Section::insert($rows);
        });
        return true;
    }
}
