<?php

declare(strict_types=1);

namespace App\Orliweb;

use Doctrine\DBAL\Statement;
use function Symfony\Component\String\u;

class ArticleImporter extends AbstractImporter
{
    public function getName(): string
    {
        return 'Article';
    }

    public function createSQLStatement(): Statement
    {
        return $this
            ->connection
            ->prepare('
                -- Insertion/Mise à jour des produits
                INSERT INTO article (id, reference, color_code, title, color, brand, saison, saisons_commerciales, forme, fabrication, semelles, tige, doublure, made_in, fermeture, semelle, ligne, genre, hauteur_talon, pictogram, type_talon, hauteur_tige, hauteur_plateforme)
                VALUES (:id, :code, :code_couleur, :title, :color, :brand, :saison, :saisons_commerciales, :forme, :type_fab, :semelle, :tige_art, :doublure, :made_in, :typ_fermeture, :nom_semelle, :lign_prod, :code_genre, :hauteur_talon, :pictogram, :type_talon, :hauteur_tige, :hauteur_plateforme)
                ON CONFLICT (id) DO UPDATE SET
                    saison = excluded.saison
                    , title = excluded.title
                    , color = excluded.color
                    , brand = excluded.brand
                    , saisons_commerciales = excluded.saisons_commerciales
                    , forme = excluded.forme
                    , fabrication = excluded.fabrication
                    , semelles = excluded.semelles
                    , tige = excluded.tige
                    , doublure = excluded.doublure
                    , made_in = excluded.made_in
                    , fermeture = excluded.fermeture
                    , semelle = excluded.semelle
                    , ligne = excluded.ligne
                    , genre = excluded.genre
                    , hauteur_talon = excluded.hauteur_talon
                    , pictogram = excluded.pictogram
                    , type_talon = excluded.type_talon
                    , hauteur_tige = excluded.hauteur_tige
                    , hauteur_plateforme = excluded.hauteur_plateforme
            ');
    }

    public function isRowImportable(array $row): bool
    {
        return 'FR' === $row['LANGUE'];
    }

    public function bindValue(Statement $stmt, array $row): void
    {
        $stmt->bindValue(':id', $row['CODE_ART_COM'].'-'.$row['CODE_COLM']);
        $stmt->bindValue(':code', $row['CODE_ART_COM']);
        $stmt->bindValue(':saison', $row['SAIS']);
        $stmt->bindValue(':title', u($row['LIB_ART_COM'])->lower()->title());
        $stmt->bindValue(':code_couleur', (int) $row['CODE_COLM']);
        $stmt->bindValue(':color', $row['LIB_COULEUR']);
        $stmt->bindValue(':brand', $row['CODE_MARQ']);
        $stmt->bindValue(':saisons_commerciales', json_encode(explode('-', $row['SAIS_COM'])));
        $stmt->bindValue(':forme', $row['CODE_FORM']);
        $stmt->bindValue(':type_fab', empty($row['TYP_FAB']) ? null : $row['TYP_FAB']);
        $stmt->bindValue(':semelle', empty($row['SEMELLE']) ? null : json_encode(explode('|', $row['SEMELLE'])));
        $stmt->bindValue(':tige_art', empty($row['TIGE_ART']) ? null : json_encode(explode('|', $row['TIGE_ART'])));
        $stmt->bindValue(':doublure', empty($row['DOUBLURE']) ? null : json_encode(explode('|', $row['DOUBLURE'])));
        $stmt->bindValue(':made_in', $row['MADE_IN']);
        $stmt->bindValue(':typ_fermeture', empty($row['TYP_FERMETURE']) ? null : $row['TYP_FERMETURE']);
        $stmt->bindValue(':nom_semelle', $row['NOM_SEMELLE']);
        $stmt->bindValue(':lign_prod', $row['LIGN_PROD']);
        $stmt->bindValue(':code_genre', empty($row['CODE_GENRE']) ? null : (int) $row['CODE_GENRE']);
        $stmt->bindValue(':hauteur_talon', empty($row['HAUT_TALON']) ? null : $row['HAUT_TALON']);
        $stmt->bindValue(':pictogram', $row['CODE_PICTO']);
        $stmt->bindValue(':type_talon', empty($row['TYPE_TALON']) ? null : $row['TYPE_TALON']);
        $stmt->bindValue(':hauteur_tige', empty($row['HAUTEUR_TIGE']) ? null : (int) $row['HAUTEUR_TIGE']);
        $stmt->bindValue(':hauteur_plateforme', empty($row['HAUTEUR_PLATEFORME']) ? null : (int) $row['HAUTEUR_PLATEFORME']);
    }

    public function getSavepointName(array $row): string
    {
        return 'sp_article';
    }
}
