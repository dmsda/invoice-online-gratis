<?php
namespace App\Controllers;

class AdminSystem extends BaseController {

    public function index() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        
        // Cek Versi CodeIgniter & PHP
        $ciVersion = \CodeIgniter\CodeIgniter::CI_VERSION;
        $phpVersion = phpversion();
        
        // Statistik Singkat DB
        $totalTables = count($db->listTables());
        
        // Ukuran Database (MySQL Specific)
        $dbName = $db->database;
        $dbSizeQuery = $db->query("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' 
            FROM information_schema.TABLES 
            WHERE table_schema = ?
        ", [$dbName])->getRow();
        
        $dbSize = $dbSizeQuery ? $dbSizeQuery->size_mb : 0;
        
        // List Migrations
        $migrations = $db->table('migrations')->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
        
        // Activity Logs (Optional, kita ambil dari session logins history / tabel user jika punya, smentara ambil 5 pendaftar terbaru aja sbagai mock aktivitas)
        $recentActivities = $db->table('users u')
                               ->select('up.business_name as name, u.email, u.created_at, u.role')
                               ->join('user_profiles up', 'up.user_id = u.id', 'left')
                               ->orderBy('u.created_at', 'DESC')
                               ->limit(5)
                               ->get()->getResultArray();

        $data = [
            'title' => 'Sistem & Audit',
            'ciVersion' => $ciVersion,
            'phpVersion' => $phpVersion,
            'dbName' => $dbName,
            'totalTables' => $totalTables,
            'dbSize' => $dbSize,
            'migrations' => $migrations,
            'recentActivities' => $recentActivities
        ];

        return view('admin/system/index', $data);
    }
}
