@props(['name', 'class' => 'w-5 h-5', 'type' => 'outline', 'ariaHidden' => 'true'])

<?php
$ariaHidden = $ariaHidden ?? 'true';
$iconMap = [
    'home' => 'HomeIcon',
    'user' => 'UserIcon',
    'users' => 'UsersIcon',
    'cog' => 'Cog6ToothIcon',
    'cog-8' => 'Cog8ToothIcon',
    'bell' => 'BellIcon',
    'bell-alert' => 'BellAlertIcon',
    'mail' => 'EnvelopeIcon',
    'mail-open' => 'EnvelopeOpenIcon',
    'phone' => 'PhoneIcon',
    'phone-arrow' => 'PhoneArrowDownLeftIcon',
    'location' => 'MapPinIcon',
    'globe' => 'GlobeAltIcon',
    'globe-americas' => 'GlobeAmericasIcon',
    'search' => 'MagnifyingGlassIcon',
    'menu' => 'Bars3Icon',
    'x' => 'XMarkIcon',
    'chevron-down' => 'ChevronDownIcon',
    'chevron-up' => 'ChevronUpIcon',
    'chevron-left' => 'ChevronLeftIcon',
    'chevron-right' => 'ChevronRightIcon',
    'arrow-right' => 'ArrowRightIcon',
    'arrow-left' => 'ArrowLeftIcon',
    'arrow-up' => 'ArrowUpIcon',
    'arrow-down' => 'ArrowDownIcon',
    'arrow-up-right' => 'ArrowUpRightIcon',
    'arrow-down-left' => 'ArrowDownLeftIcon',
    'plus' => 'PlusIcon',
    'minus' => 'MinusIcon',
    'check' => 'CheckIcon',
    'check-circle' => 'CheckCircleIcon',
    'x-circle' => 'XCircleIcon',
    'x-mark' => 'XMarkIcon',
    'download' => 'ArrowDownTrayIcon',
    'upload' => 'ArrowUpTrayIcon',
    'share' => 'ShareIcon',
    'link' => 'LinkIcon',
    'copy' => 'DocumentDuplicateIcon',
    'edit' => 'PencilIcon',
    'trash' => 'TrashIcon',
    'eye' => 'EyeIcon',
    'eye-slash' => 'EyeSlashIcon',
    'filter' => 'FunnelIcon',
    'sort' => 'ArrowsUpDownIcon',
    'refresh' => 'ArrowPathIcon',
    'save' => 'SaveIcon',
    'print' => 'PrinterIcon',
    'info' => 'InformationCircleIcon',
    'warning' => 'ExclamationTriangleIcon',
    'error' => 'XCircleIcon',
    'success' => 'CheckBadgeIcon',
    'loading' => 'ArrowPathIcon',
    'lock' => 'LockClosedIcon',
    'unlock' => 'LockOpenIcon',
    'shield' => 'ShieldCheckIcon',
    'badge' => 'BadgeCheckIcon',
    'image' => 'PhotoIcon',
    'images' => 'PhotosIcon',
    'video' => 'VideoCameraIcon',
    'play' => 'PlayIcon',
    'pause' => 'PauseIcon',
    'document' => 'DocumentIcon',
    'document-text' => 'DocumentTextIcon',
    'document-list' => 'DocumentListIcon',
    'folder' => 'FolderIcon',
    'folder-open' => 'FolderOpenIcon',
    'file' => 'DocumentIcon',
    'pdf' => 'DocumentArrowDownIcon',
    'clock' => 'ClockIcon',
    'calendar' => 'CalendarIcon',
    'calendar-days' => 'CalendarDaysIcon',
    'time' => 'ClockIcon',
    'chart-bar' => 'ChartBarIcon',
    'chart-line' => 'ChartBarIcon',
    'chart-pie' => 'ChartPieIcon',
    'trending-up' => 'TrendingUpIcon',
    'trending-down' => 'TrendingDownIcon',
    'leaf' => 'LeafIcon',
    'seedling' => 'SparklesIcon',
    'tree' => 'SparklesIcon',
    'sun' => 'SunIcon',
    'cloud' => 'CloudIcon',
    'rain' => 'CloudRainIcon',
    'water' => 'DropletIcon',
    'building' => 'BuildingOfficeIcon',
    'building-store' => 'BuildingStorefrontIcon',
    'factory' => 'BeakerIcon',
    'warehouse' => 'ArchiveBoxIcon',
    'truck' => 'TruckIcon',
    'box' => 'ArchiveBoxIcon',
    'package' => 'ArchiveBoxIcon',
    'currency' => 'CurrencyDollarIcon',
    'credit-card' => 'CreditCardIcon',
    'receipt' => 'ReceiptPercentIcon',
    'wallet' => 'WalletIcon',
    'bank' => 'BuildingOfficeIcon',
    'facebook' => 'FacebookIcon',
    'twitter' => 'TwitterIcon',
    'instagram' => 'InstagramIcon',
    'linkedin' => 'LinkedinIcon',
    'youtube' => 'YoutubeIcon',
    'github' => 'GithubIcon',
    'wheat' => 'SparklesIcon',
    'grain' => 'SparklesIcon',
    'rice' => 'SparklesIcon',
    'corn' => 'SparklesIcon',
    'tractor' => 'TruckIcon',
    'harvest' => 'SparklesIcon',
    'soil' => 'SparklesIcon',
    'fertilizer' => 'BeakerIcon',
    'pesticide' => 'ShieldCheckIcon',
    'irrigation' => 'DropletIcon',
];

$iconClass = $iconMap[$name] ?? ucfirst(strtolower($name)) . 'Icon';
$iconPath = base_path("node_modules/@heroicons/react/24/{$type}/{$iconClass}.js");

if (!file_exists($iconPath)) {
    $iconClass = ucfirst(strtolower($name)) . 'Icon';
    $iconPath = base_path("node_modules/@heroicons/react/24/{$type}/{$iconClass}.js");
}

$svgContent = '';
if (file_exists($iconPath)) {
    $jsContent = file_get_contents($iconPath);
    if (preg_match_all('/d[\s]*[:=][\s]*["\']([^"\']+)["\']/', $jsContent, $matches)) {
        $svgContent = implode(' ', $matches[1]);
    }
}

if (empty($svgContent)) {
    $svgContent = 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z';
}
?>

<svg 
    class="{{ $class }}" 
    fill="none" 
    stroke="currentColor" 
    viewBox="0 0 24 24" 
    aria-hidden="{{ $ariaHidden }}"
    stroke-width="1.5"
    stroke-linecap="round"
    stroke-linejoin="round"
>
    <path d="{{ $svgContent }}"></path>
</svg>
