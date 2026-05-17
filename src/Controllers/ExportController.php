<?php
namespace Tinhu\TaskManager\Controllers;

use Tinhu\TaskManager\Core\Database;

class ExportController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function exportExcel() {
        require_once PROJECT_ROOT . '/vendor/autoload.php';
        $projectId = $_GET['project_id'] ?? 0;

        $stmtProj = $this->db->prepare("SELECT name FROM projects WHERE id = ?");
        $stmtProj->execute([$projectId]);
        $projectName = $stmtProj->fetchColumn() ?: "Bao_Cao";

        $stmt = $this->db->prepare("
            SELECT t.id, t.title, u.fullname, t.status, t.priority, t.due_date, t.created_at
            FROM team_tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.project_id = ?
            ORDER BY t.status, t.due_date
        ");
        $stmt->execute([$projectId]);
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Tạo file CSV
        $filename = "Bao_Cao_" . preg_replace('/[^a-zA-Z0-9_-]/s', '', $projectName) . "_" . date('Y-m-d_H-i-s') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

        // Header
        fputcsv($output, ['ID', 'Tên công việc', 'Người phụ trách', 'Trạng thái', 'Độ ưu tiên', 'Hạn chót', 'Ngày tạo'], ',');

        // Data
        foreach ($tasks as $row) {
            fputcsv($output, [
                $row['id'],
                $row['title'],
                $row['fullname'] ?: 'Chưa phân công',
                $row['status'],
                $row['priority'] ?: 'Không có',
                (!empty($row['due_date']) && $row['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['due_date'])) : 'Không có',
                date('d/m/Y H:i', strtotime($row['created_at']))
            ], ',');
        }

        fclose($output);
        exit;
    }

    public function exportPersonalReport() {
        $userId = $_SESSION['user_id'] ?? 0;

        $stmt = $this->db->prepare("
            SELECT id, title, status, priority, due_date, created_at, category
            FROM tasks
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $filename = "Bao_Cao_Cong_Viec_Ca_Nhan_" . date('Y-m-d_H-i-s') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['ID', 'Tên công việc', 'Danh mục', 'Trạng thái', 'Độ ưu tiên', 'Hạn chót', 'Ngày tạo'], ',');

        foreach ($tasks as $row) {
            fputcsv($output, [
                $row['id'],
                $row['title'],
                $row['category'] ?: 'Khác',
                $row['status'],
                $row['priority'] ?: 'Không có',
                (!empty($row['due_date']) && $row['due_date'] != '0000-00-00') ? date('d/m/Y', strtotime($row['due_date'])) : 'Không có',
                date('d/m/Y H:i', strtotime($row['created_at']))
            ], ',');
        }

        fclose($output);
        exit;
    }
}
