<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    use HasFactory;
    protected $table="project";
    protected $fillable = [
        'nama', 
        'id_jenis_project', 
        'id_client', 
        'tanggal', 
        'alamat', 
        'scope', 
        'keterangan', 
        'status_project', 
        'status'
    ];

    /**
     * Function Pagination
     * getCountProject()
     * getListProject()
     * 
     */
    function columnMap(){
        $data = array(
            'nama' => 'p.tanggal',
            'tanggal' => 'p.tanggal',
            'jenis_project' => 'jp.nama',
            'nama_client' => 'c.nama',
            'alamat' => 'p.alamat',
        );
        return $data;
    }

    function getCountProject($filter){
        $key = $filter['q'];
        $idJenisProject = $filter['idJenisProject'];
        
        $q = "
            SELECT count(*) as jml from project p
            LEFT JOIN jenis_project jp ON p.id_jenis_project = jp.id
            LEFT JOIN client c ON p.id_client = c.id
            WHERE CONCAT(p.nama, jp.nama, c.nama) LIKE '%$key%' 
            AND p.status = 1
        ";

        if($idJenisProject!=""){
            $q .= " AND p.id_jenis_project = '$idJenisProject' ";
        }

        $query = DB::select($q);
        return $query[0]->jml;
    }

    function getListProject($filter){
        $key = $filter['q'];
        $idJenisProject = $filter['idJenisProject'];
        $page = $filter['page'];
        $limit = $filter['limit'];
        $sortBy = $filter['sortby'];
        $sortType = $filter['sorttype'];
        $offset = ($limit * $page) - $limit;

        $q = "
            SELECT p.*, jp.nama as jenis_project, c.nama as nama_client, (
                SELECT image FROM project_image WHERE id_project = p.id
                ORDER BY created_at ASC LIMIT 1
            ) as foto from project p
            LEFT JOIN jenis_project jp ON p.id_jenis_project = jp.id
            LEFT JOIN client c ON p.id_client = c.id
            WHERE CONCAT(p.nama, jp.nama, c.nama) LIKE '%$key%' 
            AND p.status = 1
        ";

        if($idJenisProject!=""){
            $q .= " AND p.id_jenis_project = '$idJenisProject' ";
        }

        $q .= "
            order by $sortBy $sortType
            limit $limit offset $offset
        ";

        $query = DB::select($q);
        return $query;
    }

    // Get All Project
    function getAllProject(){
        $q = DB::select("
            SELECT p.*, jp.nama as jenis_project, c.nama as nama_client, (
                SELECT image FROM project_image WHERE id_project = p.id
                ORDER BY created_at ASC LIMIT 1
            ) as foto from project p
            LEFT JOIN jenis_project jp ON p.id_jenis_project = jp.id
            LEFT JOIN client c ON p.id_client = c.id
            WHERE p.status = 1
        ");
        return $q;
    }

    function getByID($id){
        $q = DB::table('project as p')
                ->select('p.*', 'jp.nama as jenis_project', 'c.nama as nama_client')
                ->leftJoin('jenis_project as jp', 'p.id_jenis_project', '=', 'jp.id')
                ->leftJoin('client as c', 'p.id_client', '=', 'c.id')
                ->where([
                    'p.id' => $id,
                    'p.status' => '1' 
                ]);
        return $q;
    }
}
