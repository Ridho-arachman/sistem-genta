<?php
$content = file_get_contents('D:/Coding/sistem-genta/resources/views/welcome.blade.php');

$oldCss = '        .section {
            padding: 6rem 1.5rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* About Section */
        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .about-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 10px 40px rgba(229, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        .about-icon {
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Sectors Section */
        .sectors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sector-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .sector-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--sector-color);
        }

        .sector-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .sector-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .sector-card:hover .sector-icon {
            transform: scale(1.2) rotate(-5deg);
        }

        .sector-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .sector-desc {
            color: var(--color-text-muted);
            font-size: 0.9375rem;
            line-height: 1.6;
        }

        /* Programs Section */
        .programs-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }

        .program-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .program-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--program-color);
        }

        .program-card:hover {
            border-color: var(--program-color);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .program-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .program-badge {
            background: var(--program-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .program-title {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .program-desc {
            color: var(--color-text-muted);
            line-height: 1.8;
            white-space: pre-line;
        }

        /* Gallery Section */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-item {
            aspect-ratio: 4/3;
            border-radius: 1rem;
            overflow: hidden;
            background: var(--color-surface);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--color-text-muted);
        }';

$newCss = '        .section {
            padding: 5rem 1.5rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--color-secondary);
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--color-text-muted);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.7;
        }

        /* About Section */
        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .about-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 8px 30px rgba(229, 0, 0, 0.08);
            transform: translateY(-4px);
        }

        .about-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Sectors Section */
        .sectors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .sector-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sector-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--sector-color);
        }

        .sector-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }

        .sector-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .sector-card:hover .sector-icon {
            transform: scale(1.15);
        }

        .sector-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .sector-desc {
            color: var(--color-text-muted);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* Programs Section */
        .programs-container {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 1.5rem;
        }

        .program-card {
            background: white;
            border: 1px solid var(--color-border);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .program-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--program-color);
        }

        .program-card:hover {
            border-color: var(--program-color);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
        }

        .program-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .program-badge {
            background: var(--program-color);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 1.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .program-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .program-desc {
            color: var(--color-text-muted);
            line-height: 1.7;
            white-space: pre-line;
        }

        /* Gallery Section */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .gallery-item {
            aspect-ratio: 4/3;
            border-radius: 0.75rem;
            overflow: hidden;
            background: var(--color-surface);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .gallery-item:hover {
            transform: scale(1.015);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }';

$content = file_get_contents('D:/Coding/sistem-genta/resources/views/welcome.blade.php');
$content = str_replace($oldCss, $newCss, $content);
file_put_contents('D:/Coding/sistem-genta/resources/views/welcome.blade.php', $content);
echo 'Updated CSS styles';