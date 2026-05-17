-- Bảng cấu hình hệ thống
CREATE TABLE IF NOT EXISTS system_configs (
    config_key VARCHAR(100) PRIMARY KEY,
    config_value TEXT
);

-- Chèn cấu hình mặc định
INSERT IGNORE INTO system_configs (config_key, config_value) VALUES
('task_statuses', 'To Do,In Progress,Done'),
('task_priorities', 'Low,Medium,High'),
('task_labels', 'Công việc,Học tập,Sức khỏe,Tài chính,Khác'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_user', 'your-email@gmail.com');
