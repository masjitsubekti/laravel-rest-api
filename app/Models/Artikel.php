<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Artikel extends Model
{
    use HasFactory;
    protected $table="artikel";
    protected $fillable = [
        'title', 
        'deskripsi', 
        'image', 
        'is_publish', 
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Function Pagination
     * getCountArtikel()
     * getListArtikel()
     * 
     */

    function getCountArtikel($filter){
        $key = $filter['q'];
        $isPublish = $filter['isPublish'];
        
        $q = "
            SELECT count(*) as jml from artikel
            WHERE CONCAT(title, deskripsi) LIKE '%$key%' 
            AND status = '1'
        ";

        if($isPublish!=""){
            $q .= " AND is_publish = '$isPublish' ";
        }

        $query = DB::select($q);
        return $query[0]->jml;
    }

    function getListArtikel($filter){
        $key = $filter['q'];
        $isPublish = $filter['isPublish'];
        $page = $filter['page'];
        $limit = $filter['limit'];
        $sortBy = $filter['sortby'];
        $sortType = $filter['sorttype'];
        $offset = ($limit * $page) - $limit;

        $q = "
            SELECT * FROM artikel
            WHERE CONCAT(title, deskripsi) LIKE '%$key%' 
            AND status = '1'
        ";

        if($isPublish!=""){
            $q .= " AND is_publish = '$isPublish' ";
        }

        $q .= "
            order by $sortBy $sortType
            limit $limit offset $offset
        ";

        $query = DB::select($q);
        return $query;
    }
}
