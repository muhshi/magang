<?php

$dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('app/Filament'));
$files = new \RegexIterator($dir, '/\.php$/');

$replacements = [
    'protected static ?string $navigationIcon' => 'protected static string | \BackedEnum | null $navigationIcon',
    'protected static ?string $activeNavigationIcon' => 'protected static string | \BackedEnum | null $activeNavigationIcon',
    'protected static ?string $navigationGroup' => 'protected static string | \UnitEnum | null $navigationGroup',
];

$regexReplacements = [
    '/use Filament\\\\Forms\\\\Form;/' => 'use Filament\Schemas\Schema;',
    '/use Filament\\\\Infolists\\\\Infolist;/' => 'use Filament\Schemas\Schema;',
    '/(public|protected) (static )?function form\(Form (\$\w+)\): Form/' => '$1 $2function form(Schema $3): Schema',
    '/(public|protected) (static )?function infolist\(Infolist (\$\w+)\): Infolist/' => '$1 $2function infolist(Schema $3): Schema',
    '/protected static \?string \$view/' => 'protected string $view',
    '/protected static string \$view/' => 'protected string $view',
    '/protected static \?string \$title/' => 'protected ?string $title',
    '/protected static string \$title/' => 'protected ?string $title',
    '/protected static \?string \$heading/' => 'protected ?string $heading',
    '/protected static string \$heading/' => 'protected ?string $heading',
];

foreach ($files as $file) {
    if ($file->isFile()) {
        $content = file_get_contents($file->getPathname());
        $modified = false;
        
        foreach ($replacements as $search => $replace) {
            if (strpos($content, $search) !== false) {
                $content = str_replace($search, $replace, $content);
                $modified = true;
            }
        }

        foreach ($regexReplacements as $pattern => $replace) {
            $newContent = preg_replace($pattern, $replace, $content);
            if ($newContent !== $content) {
                $content = $newContent;
                $modified = true;
            }
        }
        
        if ($modified) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated " . $file->getPathname() . "\n";
        }
    }
}
