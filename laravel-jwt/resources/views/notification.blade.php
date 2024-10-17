<!DOCTYPE html>
<html>
<head>
    <title>تقرير المهام اليومي</title>
</head>
<body>
    <h1>تقرير المهام اليومي</h1>
    <p>مرحباً {{ $user->name }},</p>
    <p>هذه قائمة المهام الخاصة بك لهذا اليوم:</p>

    <ul>
        @foreach($tasks as $task)
            <li>{{ $task->title }} - الحالة: {{ $task->status }}</li>
        @endforeach
    </ul>

    <p>شكراً لك،</p>
    <p>فريق إدارة المهام</p>
</body>
</html>
