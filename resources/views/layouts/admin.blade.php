<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - QuotationSystem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-white h-screen fixed flex flex-col">
        <div class="p-4 border-b border-gray-800">
            <a href="/admin/dashboard" class="text-lg font-bold text-indigo-400">QS Admin</a>
        </div>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <div class="text-xs text-gray-500 uppercase tracking-wider mb-1 px-3">Main</div>
            <x-nav-link href="/admin/dashboard" icon="dashboard">Dashboard</x-nav-link>

            <div class="text-xs text-gray-500 uppercase tracking-wider mt-4 mb-1 px-3">Management</div>
            @if(in_array('companies.manage', $userPermissions))
                <x-nav-link href="/admin/companies" active="admin/companies*" icon="building">Companies</x-nav-link>
            @endif
            @if(in_array('packages.manage', $userPermissions))
                <x-nav-link href="/admin/packages" active="admin/packages*" icon="package">Packages</x-nav-link>
            @endif
            @if(in_array('currencies.manage', $userPermissions))
                <x-nav-link href="/admin/currencies" active="admin/currencies*" icon="currency">Currencies</x-nav-link>
            @endif
            @if(in_array('taxes.manage', $userPermissions))
                <x-nav-link href="/admin/taxes" active="admin/taxes*" icon="tax">Taxes</x-nav-link>
            @endif

            <div class="text-xs text-gray-500 uppercase tracking-wider mt-4 mb-1 px-3">Oversight</div>
            @if(in_array('quotations.view', $userPermissions))
                <x-nav-link href="/admin/quotations" active="admin/quotations*" icon="quote">Quotations</x-nav-link>
            @endif
            @if(in_array('users.manage', $userPermissions))
                <x-nav-link href="/admin/users" active="admin/users*" icon="users">Admin Users</x-nav-link>
            @endif
            @if(in_array('companies.manage', $userPermissions))
                <x-nav-link href="/admin/company-users" active="admin/company-users*" icon="users-team">Company Users</x-nav-link>
            @endif

            <div class="text-xs text-gray-500 uppercase tracking-wider mt-4 mb-1 px-3">Reports & Tools</div>
            @if(in_array('reports.view', $userPermissions))
                <x-nav-link href="/admin/reports" active="admin/reports*" icon="report">Reports & Exports</x-nav-link>
            @endif
            @if(in_array('activity.view', $userPermissions))
                <x-nav-link href="/admin/activity-log" active="admin/activity-log*" icon="clock">Activity Log</x-nav-link>
            @endif
            @if(in_array('health.view', $userPermissions))
                <x-nav-link href="/admin/health" active="admin/health*" icon="heart">System Health</x-nav-link>
            @endif

            <div class="text-xs text-gray-500 uppercase tracking-wider mt-4 mb-1 px-3">Content</div>
            @if(in_array('pages.manage', $userPermissions))
                <x-nav-link href="/admin/pages" active="admin/pages*" icon="page">Pages</x-nav-link>
            @endif
            @if(in_array('email_templates.manage', $userPermissions))
                <x-nav-link href="/admin/email-templates" active="admin/email-templates*" icon="mail">Email Templates</x-nav-link>
            @endif

            <div class="text-xs text-gray-500 uppercase tracking-wider mt-4 mb-1 px-3">System</div>
            @if(in_array('settings.manage', $userPermissions))
                <x-nav-link href="/admin/settings" active="admin/settings*" icon="settings">Settings</x-nav-link>
            @endif
        </nav>
        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs text-gray-500">{{ auth()->user()->name }}</div>
                <div class="relative" id="notification-bell">
                    <button onclick="document.getElementById('notification-dropdown').classList.toggle('hidden')" class="text-gray-400 hover:text-white relative">
                        <x-icon name="bell" class="w-4 h-4" />
                        <span id="notification-count" class="hidden absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] rounded-full flex items-center justify-center">0</span>
                    </button>
                    <div id="notification-dropdown" class="hidden absolute right-0 bottom-8 w-72 bg-white rounded-lg shadow-lg border z-50">
                        <div class="p-3 border-b text-xs font-semibold text-gray-700">Notifications</div>
                        <div id="notification-list" class="max-h-64 overflow-y-auto">
                            <div class="p-3 text-xs text-gray-400 text-center">No notifications yet</div>
                        </div>
                    </div>
                </div>
            </div>
            <a href="/settings/profile" class="block text-xs text-gray-400 hover:text-white mb-2">Profile Settings</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full text-left text-sm text-gray-400 hover:text-white">Logout</button>
            </form>
        </div>
    </aside>
    <div class="ml-64 flex-1 p-6">
        <x-alert type="success" />
        <x-alert type="error" />
        @yield('content')
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private('admin')
                .listen('.company.status.changed', function(e) {
                    addNotification('Company ' + e.name + ' status changed to ' + e.status);
                });

            @if(auth()->user()->company_id)
            window.Echo.private('company.{{ auth()->user()->company_id }}')
                .listen('.quotation.status.changed', function(e) {
                    addNotification('Quotation ' + e.quote_number + ' is now ' + e.status);
                })
                .listen('.package.assigned', function(e) {
                    addNotification('Package ' + e.package_name + ' assigned to ' + e.company_name);
                });
            @endif
        }
    });

    var notificationCount = 0;
    function addNotification(message) {
        var list = document.getElementById('notification-list');
        var count = document.getElementById('notification-count');
        var empty = list.querySelector('.text-center');
        if (empty) empty.remove();

        var item = document.createElement('div');
        item.className = 'p-3 text-xs text-gray-700 border-b hover:bg-gray-50';
        item.innerHTML = '<div>' + message + '</div><div class="text-gray-400 mt-1">Just now</div>';
        list.insertBefore(item, list.firstChild);

        notificationCount++;
        count.textContent = notificationCount;
        count.classList.remove('hidden');
    }
    </script>
</body>
</html>
