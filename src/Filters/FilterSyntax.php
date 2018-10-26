<?php
/**
 * @copyright 2018 Greg Roach <fisharebest@gmail.com>
 * @license   GPLv3+
 */
declare(strict_types=1);

namespace Fisharebest\LibGedcom\Filters;

/**
 * Filter a GEDCOM data stream.
 *
 * Convert to UTF8
 * Normalize whitespace
 * Normalize line endings
 * Merge CONC records
 */
class FilterSyntax extends AbstractGedcomFilter {
    /** GEDCOM tag-names and their corresponding GEDCOM tags */
    const GEDCOM_TAG_NAMES = [
        'ABBREVIATION'        => 'ABBR',
        'ADDRESS'             => 'ADDR',
        'ADDRESS1'            => 'ADR1',
        'ADDRESS2'            => 'ADR2',
        'ADDRESS3'            => 'ADR3',
        'ADOPTION'            => 'ADOP',
        'ADULT_CHRISTENING'   => 'CHRA',
        'AGENCY'              => 'AGNC',
        'ALIAS'               => 'ALIA',
        'ANCESTORS'           => 'ANCE',
        'ANCES_INTEREST'      => 'ANCI',
        'ANNULMENT'           => 'ANUL',
        'ASSOCIATES'          => 'ASSO',
        'AUTHOR'              => 'AUTH',
        'BAPTISM'             => 'BAPM',
        'BAPTISM_LDS'         => 'BAPL',
        'BAR_MITZVAH'         => 'BARM',
        'BAS_MITZVAH'         => 'BASM',
        'BIRTH'               => 'BIRT',
        'BLESSING'            => 'BLES',
        'BURIAL'              => 'BURI',
        'CALL_NUMBER'         => 'CALN',
        'CASTE'               => 'CAST',
        'CAUSE'               => 'CAUS',
        'CENSUS'              => 'CENS',
        'CHANGE'              => 'CHAN',
        'CHARACTER'           => 'CHAR',
        'CHILD'               => 'CHIL',
        'CHILDREN_COUNT'      => 'NCHI',
        'CHRISTENING'         => 'CHR',
        'CONCATENATION'       => 'CONC',
        'CONFIRMATION'        => 'CONF',
        'CONFIRMATION_LDS'    => 'CONL',
        'CONTINUED'           => 'CONT',
        'COPYRIGHT'           => 'COPR',
        'CORPORATE'           => 'CORP',
        'COUNTRY'             => 'CTRY',
        'CREMATION'           => 'CREM',
        'DEATH'               => 'DEAT',
        '_DEATH_OF_SPOUSE'    => '_DETS',
        '_DEGREE'             => '_DEG',
        'DESCENDANTS'         => 'DESC',
        'DESCENDANT_INT'      => 'DESI',
        'DESTINATION'         => 'DEST',
        'DIVORCE'             => 'DIV',
        'DIVORCE_FILED'       => 'DIVF',
        'EDUCATION'           => 'EDUC',
        'EMIGRATION'          => 'EMIG',
        'ENDOWMENT'           => 'ENDL',
        'ENGAGEMENT'          => 'ENGA',
        'EVENT'               => 'EVEN',
        'FACSIMILE'           => 'FAX',
        'FAMILY'              => 'FAM',
        'FAMILY_CHILD'        => 'FAMC',
        'FAMILY_FILE'         => 'FAMF',
        'FAMILY_SPOUSE'       => 'FAMS',
        'FIRST_COMMUNION'     => 'FCOM',
        '_FILE'               => 'FILE',
        'FORMAT'              => 'FORM',
        'GEDCOM'              => 'GEDC',
        'GIVEN_NAME'          => 'GIVN',
        'GRADUATION'          => 'GRAD',
        'HEADER'              => 'HEAD',
        'HUSBAND'             => 'HUSB',
        'IDENT_NUMBER'        => 'IDNO',
        'IMMIGRATION'         => 'IMMI',
        'INDIVIDUAL'          => 'INDI',
        'LANGUAGE'            => 'LANG',
        'LATITUDE'            => 'LATI',
        'LONGITUDE'           => 'LONG',
        'MARRIAGE'            => 'MARR',
        'MARRIAGE_BANN'       => 'MARB',
        'MARRIAGE_COUNT'      => 'NMR',
        'MARRIAGE_CONTRACT'   => 'MARC',
        'MARRIAGE_LICENSE'    => 'MARL',
        'MARRIAGE_SETTLEMENT' => 'MARS',
        'MEDIA'               => 'MEDI',
        '_MEDICAL'            => '_MDCL',
        '_MILITARY_SERVICE'   => '_MILT',
        'NAME_PREFIX'         => 'NPFX',
        'NAME_SUFFIX'         => 'NSFX',
        'NATIONALITY'         => 'NATI',
        'NATURALIZATION'      => 'NATU',
        'NICKNAME'            => 'NICK',
        'OBJECT'              => 'OBJE',
        'OCCUPATION'          => 'OCCU',
        'ORDINANCE'           => 'ORDI',
        'ORDINATION'          => 'ORDN',
        'PEDIGREE'            => 'PEDI',
        'PHONE'               => 'PHON',
        'PHONETIC'            => 'FONE',
        'PHY_DESCRIPTION'     => 'DSCR',
        'PLACE'               => 'PLAC',
        'POSTAL_CODE'         => 'POST',
        'PROBATE'             => 'PROB',
        'PROPERTY'            => 'PROP',
        'PUBLICATION'         => 'PUBL',
        'QUALITY_OF_DATA'     => 'QUAL',
        'REC_FILE_NUMBER'     => 'RFN',
        'REC_ID_NUMBER'       => 'RIN',
        'REFERENCE'           => 'REFN',
        'RELATIONSHIP'        => 'RELA',
        'RELIGION'            => 'RELI',
        'REPOSITORY'          => 'REPO',
        'RESIDENCE'           => 'RESI',
        'RESTRICTION'         => 'RESN',
        'RETIREMENT'          => 'RETI',
        'ROMANIZED'           => 'ROMN',
        'SEALING_CHILD'       => 'SLGC',
        'SEALING_SPOUSE'      => 'SLGS',
        'SOC_SEC_NUMBER'      => 'SSN',
        'SOURCE'              => 'SOUR',
        'STATE'               => 'STAE',
        'STATUS'              => 'STAT',
        'SUBMISSION'          => 'SUBN',
        'SUBMITTER'           => 'SUBM',
        'SURNAME'             => 'SURN',
        'SURN_PREFIX'         => 'SPFX',
        'TEMPLE'              => 'TEMP',
        'TITLE'               => 'TITL',
        'TRAILER'             => 'TRLR',
        'VERSION'             => 'VERS',
        'WEB'                 => 'WWW',
    ];

    /**
     * Apply text filters to the data.
     *
     * @param string $data
     *
     * @return string
     */
    protected function filterData(string $data): string {
        // The order of these is important.
        $data = $this->mergeConc($data);
        $data = $this->fixFtmTagNames($data);

        return $data;
    }

    /**
     * Merge concatenation records.
     *
     * @param string $gedcom_record
     *
     * @return string
     */
    private function mergeConc(string $gedcom_record): string {
        return preg_replace('/\n\d (?:@[^@]+@ )?CONC ?/', '', $gedcom_record);
    }

    /**
     * FamilyTreeMaker creates files with tag-names instead of tags.
     *
     * @param string $gedcom_record
     *
     * @return string
     */
    private function fixFtmTagNames(string $gedcom_record): string {
        return preg_replace_callback('/(\n\d+ )(\w+)/', [$this, 'fixFtmNamesCallback'], $gedcom_record);
    }

    /**
     * Replace GEDCOM tag-names with GEDCOM tags.
     *
     * @param array $matches
     *
     * @return string
     */
    private function fixFtmNamesCallback(array $matches): string {
        if (array_key_exists($matches[2], self::GEDCOM_TAG_NAMES)) {
            return $matches[1] . self::GEDCOM_TAG_NAMES[$matches[2]];
        } else {
            return $matches[0];
        }
    }
}
