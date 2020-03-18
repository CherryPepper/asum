<?php
namespace App\Repositories;

use App\User;

class NavigationRepository{
    public static function getNavigation(User $user){
        $navigation = [];

        if(in_array($user->role->slug, ['administrator', 'manager'])){
            $navigation[] = [
                'url' => '#clients',
                'title' => 'Клиенты',
                'ico' => 'fa fa-user-circle-o',
                'id' => 'clients',

                'clients' => [
                    [
                        'url' => route('client.create'),
                        'title' => 'Добавить абонента',
                        'ico' => 'fa fa-user-plus',
                    ],
                    [
                        'url' => route('client.list'),
                        'title' => 'Найти абонента',
                        'ico' => 'fa fa-search',
                    ],
                    [
                        'url' => route('ometers.total_value'),
                        'title' => 'Внесение показаний',
                        'ico' => 'fa fa-plus',
                    ],
                ]
            ];
        }
        if(in_array($user->role->slug, ['administrator', 'manager'])){
            $navigation[] = [
                'url' => '#tasks',
                'title' => 'Задания',
                'ico' => 'fa fa-tasks',
                'id' => 'tasks',

                'tasks' => [
                    [
                        'url' => route('tasks.list', ['type' => 'completed']),
                        'title' => 'Выполненные задания',
                        'ico' => 'fa fa-check-square-o',
                    ],
                    [
                        'url' => route('tasks.list', ['type' => 'process']),
                        'title' => 'Текущие задания',
                        'ico' => 'fa fa-refresh',
                    ],
                    [
                        'url' => route('task.create'),
                        'title' => 'Добавить задание',
                        'ico' => 'fa fa-plus',
                    ],
                ]
            ];

            if(in_array($user->role->slug, ['administrator'])){
                $navigation[] = [
                    'url' => '#staff',
                    'title' => 'Персонал',
                    'ico' => 'fa fa-users',
                    'id' => 'staff',

                    'staff' => [
                        [
                            'url' => route('employer.create'),
                            'title' => 'Добавить сотрудника',
                            'ico' => 'fa fa-user-plus',
                        ],
                        [
                            'url' => route('employer.list'),
                            'title' => 'Список сотрудников',
                            'ico' => 'fa fa-list-ul',
                        ],
                    ]
                ];
            }

            $navigation[] = [
                'url' => '#rates',
                'title' => 'Тарифы',
                'ico' => 'fa fa-calculator',
                'id' => 'rates',

                'rates' => [
                    [
                        'url' => route('rate.create'),
                        'title' => 'Добавить тариф',
                        'ico' => 'fa fa-plus',
                    ],
                    [
                        'url' => route('rate.list'),
                        'title' => 'Список тарифов',
                        'ico' => 'fa fa-list-ul',
                    ],
                ]
            ];

            $navigation[] = [
                'url' => '#reports',
                'title' => 'Отчеты',
                'ico' => 'fa fa-bar-chart-o',
                'id' => 'reports',

                'reports' => [
                    [
                        'url' => route('report.general'),
                        'title' => 'Развернутый отчет',
                        'ico' => 'fa fa-sitemap',
                    ],
                    [
                        'url' => route('report.consumption'),
                        'title' => 'Потребление энергии',
                        'ico' => 'fa fa-area-chart',
                    ],
                    [
                        'url' => route('report.loss'),
                        'title' => 'Кражи/Потери',
                        'ico' => 'fa fa-balance-scale',
                    ],
                    [
                        'url' => route('report.staff'),
                        'title' => 'Сотрудники',
                        'ico' => 'fa fa-users',
                    ],
                    [
                        'url' => route('report.other_meters'),
                        'title' => 'Другие счетчики',
                        'ico' => 'fa fa-line-chart',
                    ],
                ]
            ];
        }

        if(in_array($user->role->slug, ['administrator', 'technician'])){
            $navigation[] = [
                'url' => '#technician',
                'title' => 'Тех раздел',
                'ico' => 'fa fa-wrench',
                'id' => 'technician',

                'technician' => [
                    [
                        'url' => route('meters.add.control'),
                        'title' => 'Контрольный счетчик',
                        'ico' => 'fa fa-tachometer',
                    ],
                    [
                        'url' => route('meters.registration'),
                        'title' => 'Регистрация счетчиков',
                        'ico' => 'fa fa-plug',
                    ],
                    [
                        'url' => route('meters.structure'),
                        'title' => 'Структура счетчиков',
                        'ico' => 'fa fa-sitemap',
                    ]
                ]
            ];

            if(in_array($user->role->slug, ['technician'])){
                $navigation[0]['technician'][] = [
                    'url' => route('report.loss'),
                    'title' => 'Кражи/Потери',
                    'ico' => 'fa fa-balance-scale',
                ];

                $navigation[] = [
                    'url' => '#tasks',
                    'title' => 'Задания',
                    'ico' => 'fa fa-tasks',
                    'id' => 'tasks',

                    'tasks' => [
                        [
                            'url' => route('tasks.list', ['type' => 'completed']),
                            'title' => 'Выполненные задания',
                            'ico' => 'fa fa-check-square-o',
                        ],
                        [
                            'url' => route('tasks.list', ['type' => 'process']),
                            'title' => 'Текущие задания',
                            'ico' => 'fa fa-refresh',
                        ],
                    ]
                ];
            }
        }

        if(in_array($user->role->slug, ['administrator'])){
            $navigation[] = [
                'url' => '#monitoring',
                'title' => 'Мониторинг',
                'ico' => 'fa fa-line-chart',
                'id' => 'monitoring',

                'monitoring' => [
                    [
                        'url' => route('monitoring.requests'),
                        'title' => 'Запросы',
                        'ico' => 'fa fa-paper-plane',
                    ],
                    [
                        'url' => route('monitoring.meters'),
                        'title' => 'Счетчики',
                        'ico' => 'fa fa-sitemap',
                    ],
                ]
            ];

            $navigation[] = [
                'url' => route('notifications.send'),
                'title' => 'Уведомления',
                'ico' => 'fa fa-bell',
                'id' => 'notifications'
            ];
        }

        if(in_array($user->role->slug, ['user'])){
            $navigation[] = [
                'url' => route('user.info'),
                'title' => 'Общая информация',
                'ico' => 'fa fa-info-circle',
                'id' => 'info'
            ];
            $navigation[] = [
                'url' => route('user.other_meters'),
                'title' => 'Другие счетчики',
                'ico' => 'fa fa-sitemap',
                'id' => 'other-meters'
            ];
            $navigation[] = [
                'url' => route('user.notifications'),
                'title' => 'Уведомления',
                'ico' => 'fa fa-bell',
                'id' => 'notifications'
            ];
        }

        if(in_array($user->role->slug, ['tozelesh'])){
            $navigation[] = [
                'url' => route('tozelesh.map'),
                'title' => 'Карта',
                'ico' => 'fa fa-map-o',
                'id' => 'map'
            ];

            $navigation[] = [
                'url' => route('report.tozelesh'),
                'title' => 'Отчет',
                'ico' => 'fa fa-bar-chart-o',
                'id' => 'tozelesh-report'
            ];
        }
        return $navigation;
    }
}
