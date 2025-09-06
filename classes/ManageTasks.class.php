<?php

class ManageTasks
{
    public static function load(): void {
        if (DATA::getMethod() == 'POST') {
            self::validateTask();
            self::createTask();
            self::updateTask();
            self::deleteTask();
            self::createTaskTable();
            self::updateTaskTable();
            self::deleteTaskTable();
        }
    }

    public static function validateTask(): void {
        if (DATA::issetPost('validateTask') && DATA::isPost('taskId')) {
            $filter = ['id' => DATA::getPost('taskId')];
            if (DATA::getPost('validateTask') == 1) {
                $data = ['validated' => DATA::getPost('validateTask'), 'date_validated' => date("c")];
            } else {
                $data = ['validated' => DATA::getPost('validateTask'), 'date_validated' => `NULL`];
            }
            Generique::update('tasks', 'graphene_bsm', $filter, $data);
            exit;
        }
    }

    public static function createTask(): void {
        if (DATA::isPost('taskTitle') && DATA::isPost('tableId')) {
            $data = ['creator_id' => USER::getId(), 'title' => DATA::getPost('taskTitle'), 'table_id' => DATA::getPost('tableId')];
            if (DATA::isPost('dateDeadline'))
                $data['date_deadline'] = DATA::getPost('dateDeadline');
            if (DATA::isPost('dateReminder'))
                $data['date_reminder'] = DATA::getPost('dateReminder');
            if (DATA::isPost('users'))
                $data['users'] = DATA::getPost('users');
            if (DATA::isPost('priority'))
                $data['priority'] = DATA::getPost('priority');
            if (DATA::isPost('description'))
                $data['description'] = DATA::getPost('description');
            /*
            require_once('./classes/UtilsCalendarEvent.class.php');
            $data['calendar_event_id'] = calendarEntry::calEvent("tasks", "", DATA::getPost('taskTitle'), DATA::getPost('description'), "", DATA::getPost('dateDeadline'), DATA::getPost('dateDeadline'), DATA::getPost('dateReminder'));
            //*/
            Generique::insert('tasks', 'graphene_bsm', $data);
            echo Generique::selectMaxId('tasks', 'graphene_bsm');
            exit;
        }
    }

    public static function updateTask(): void {
        if (DATA::isPost('taskTitle') && DATA::isPost('taskId')) {
            $filter = ['id' => DATA::getPost('taskId')];
            $data = ['title' => DATA::getPost('taskTitle')];
            if (DATA::isPost('dateDeadline'))
                $data['date_deadline'] = DATA::getPost('dateDeadline');
            else
                $data['date_deadline'] = `NULL`;
            if (DATA::issetPost('dateReminder'))
                $data['date_reminder'] = DATA::getPost('dateReminder');
            if (DATA::issetPost('users'))
                $data['users'] = DATA::getPost('users');
            if (DATA::issetPost('priority'))
                $data['priority'] = DATA::getPost('priority');
            if (DATA::issetPost('description'))
                $data['description'] = DATA::getPost('description');
            Generique::update('tasks', 'graphene_bsm', $filter, $data);
            exit;
        }
    }

    public static function deleteTask(): void {
        if (DATA::isPost('removeTask')) {
            $filter = ['id' => DATA::getPost('removeTask')];
            $data = ['deleted' => true];
            Generique::update('tasks', 'graphene_bsm', $filter, $data);
            exit;
        }
    }

    public static function createTaskTable(): void {
        if (DATA::isPost('createTaskTable')) {
            $data = ['title' => DATA::getPost('createTaskTable'), 'icon' => 'view_agenda', 'creator_id' => USER::getId()];
            Generique::insert('tasks_tables', 'graphene_bsm', $data);
            echo Generique::selectMaxId('tasks_tables', 'graphene_bsm');
            exit;
        }
    }

    public static function updateTaskTable(): void {
        if (DATA::isPost('taskTableId') && DATA::issetPost('taskTableTitle')) {
            $filter = ['id' => DATA::getPost('taskTableId')];
            $data = ['title' => DATA::getPost('taskTableTitle')];
            Generique::update('tasks_tables', 'graphene_bsm', $filter, $data);
            exit;
        }
    }

    public static function deleteTaskTable(): void {
        if (DATA::isPost('removeTaskTable')) {
            $filter = ['id' => DATA::getPost('removeTaskTable')];
            $data = ['deleted' => true];
            Generique::update('tasks_tables', 'graphene_bsm', $filter, $data);
            exit;
        }
    }
}
