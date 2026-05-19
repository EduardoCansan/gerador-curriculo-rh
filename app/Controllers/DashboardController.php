<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $curriculoModel = new Curriculo();
        $stats   = $curriculoModel->estatisticas();
        $ultimos = $curriculoModel->ultimos(8);

        $userModel  = new User();
        $totalUsers = $userModel->count();

        $this->view('dashboard/index', [
            'stats'      => $stats,
            'ultimos'    => $ultimos,
            'totalUsers' => $totalUsers,
            'pageTitle'  => 'Dashboard',
        ]);
    }
}
