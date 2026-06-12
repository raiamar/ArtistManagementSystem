<?php

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(string $token) : bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireAuth() : void{
    if(!isLoggedIn())
    {
        header('Location: ' . APP_URL . 'login.php');
        exit;
    }
}

function hasRole(string ...$roles) : bool{
    $user = currenctUser();
    return $user !== null && in_array($user['role'], $roles, true);
}

function currenctUser(): ?array{
    if(!isLoggedIn())
        return null;

    return[
        'id'=>$_SESSION['user_id'],
        'name'=>$_SESSION['user_name'],
        'email'=>$_SESSION['user_email'],
        'role'=>$_SESSION['user_role'],
    ];
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function paginate(string $table, string $where = '', array $params = [], int $perPage = 10, ?int $page = null) : array
{
    $page = $page ?? max(1, (int)($_GET['p'] ?? 1));
    $offset = ($page - 1) * $perPage;

    $countSql = "SELECT COUNT(*) as total FROM $table";

    if($where)
        $countSql .= " WHERE $where";

    $total = (int) Database::fetchOne($countSql, $params)['total'];
    $lastPage = max(1, (int) ceil($total / $perPage));

    $dataSql = "SELECT * FROM $table";
    if($where)
        $dataSql .= " WHERE $where";

    $dataSql .= " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
    $data = Database::fetchAll($dataSql, $params);

    return[
        'data' => $data,
        'current_page' => $page,
        'per_page' => $perPage,
        'last_page' => $lastPage,
        'total' => $total,
        'has_prev' => $page > 1,
        'has_next' => $page < $lastPage,
        'prev_page' => $page - 1,
        'next_page' => $page + 1,
    ];
}

function paginationLinks(array $paginator, string $baseUrl = '?') : string
{
    if($paginator['last_page'] <= 1)
        return '';
    $html = '<div class="flex justify-center mt-4"><nav class="flex items-center gap-1">';

    if($paginator['has_prev'])
    {
        $html .= '<a href="' . $baseUrl . '&p=' . $paginator['prev_page'] . '" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-100 transition">&laquo; Previous</a>';
    }else{
        $html .= '<span class="px-3 py-1 border border-gray-200 rounded text-sm text-gray-400">&laquo; Previous</span>';
    }

    for($i = 1; $i <= $paginator['last_page']; $i++)
    {
        if ($i === $paginator['current_page']) {
            $html .= '<span class="px-3 py-1 border border-blue-500 rounded text-sm bg-blue-500 text-white">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . '&p=' . $i . '" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-100 transition">' . $i . '</a>';
        }
    }

    if ($paginator['has_next']) {
        $html .= '<a href="' . $baseUrl . '&p=' . $paginator['next_page'] . '" class="px-3 py-1 border border-gray-300 rounded text-sm text-gray-700 hover:bg-gray-100 transition">Next &raquo;</a>';
    } else {
        $html .= '<span class="px-3 py-1 border border-gray-200 rounded text-sm text-gray-400">Next &raquo;</span>';
    }

    $html .= '</nav></div>';

    return $html;
}

function csvExport(array $data, string $filename): void{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fwrite($output, "\xEF\xBB\xBF");
    if(!empty($data)){
        fputcsv($output, array_keys($data[0]));
        foreach($data as $row)
        {
            fputcsv($output, $row);
        }
    }
    fclose($output);
    exit;
}
