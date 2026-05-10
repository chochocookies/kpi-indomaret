<?php
// models/KpiModel.php
class KpiModel {
    protected Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ============================================================
    // TOKO
    // ============================================================
    public function getAllToko(): array {
        return $this->db->fetchAll("SELECT * FROM toko WHERE aktif = 1 ORDER BY nama_toko");
    }

    public function getToko(string $kode): ?array {
        return $this->db->fetch("SELECT * FROM toko WHERE kode_toko = ?", [$kode]);
    }

    // ============================================================
    // SPD
    // ============================================================
    public function getTargetSpd(string $kode, int $tahun, int $bulan): ?array {
        return $this->db->fetch(
            "SELECT * FROM target_spd WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
    }

    public function getAktualSpdBulanan(string $kode, int $tahun, int $bulan): array {
        return $this->db->fetchAll(
            "SELECT * FROM aktual_spd_harian 
             WHERE kode_toko=? AND YEAR(tanggal)=? AND MONTH(tanggal)=?
             ORDER BY tanggal ASC",
            [$kode, $tahun, $bulan]
        );
    }

    public function getAktualSpdAggregate(string $kode, int $tahun, int $bulan): array {
        $row = $this->db->fetch(
            "SELECT 
                SUM(aktual_offline) as total_offline,
                SUM(aktual_online) as total_online,
                SUM(aktual_produk_khusus) as total_khusus,
                SUM(aktual_dry) as total_dry,
                SUM(aktual_perishable) as total_perishable,
                SUM(aktual_virtual) as total_virtual,
                COUNT(*) as jumlah_hari
             FROM aktual_spd_harian 
             WHERE kode_toko=? AND YEAR(tanggal)=? AND MONTH(tanggal)=?",
            [$kode, $tahun, $bulan]
        );
        return $row ?: [];
    }

    public function saveAktualSpd(string $kode, string $tanggal, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM aktual_spd_harian WHERE kode_toko=? AND tanggal=?",
            [$kode, $tanggal]
        );
        if ($existing) {
            $this->db->execute(
                "UPDATE aktual_spd_harian SET 
                    aktual_offline=?, aktual_online=?, aktual_produk_khusus=?,
                    aktual_dry=?, aktual_perishable=?, aktual_virtual=?, catatan=?
                 WHERE kode_toko=? AND tanggal=?",
                [$data['offline'], $data['online'], $data['khusus'],
                 $data['dry'], $data['perishable'], $data['virtual'] ?? 0,
                 $data['catatan'] ?? '', $kode, $tanggal]
            );
        } else {
            $this->db->insert(
                "INSERT INTO aktual_spd_harian 
                    (kode_toko, tanggal, aktual_offline, aktual_online, aktual_produk_khusus, aktual_dry, aktual_perishable, aktual_virtual, catatan)
                 VALUES (?,?,?,?,?,?,?,?,?)",
                [$kode, $tanggal, $data['offline'], $data['online'], $data['khusus'],
                 $data['dry'], $data['perishable'], $data['virtual'] ?? 0, $data['catatan'] ?? '']
            );
        }
        return true;
    }

    public function saveTargetSpd(string $kode, int $tahun, int $bulan, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM target_spd WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
        if ($existing) {
            $this->db->execute(
                "UPDATE target_spd SET tgt_hari=?, target_offline=?, target_online=?,
                    target_dry=?, target_perishable=?, target_produk_khusus=?, target_virtual=?
                 WHERE kode_toko=? AND tahun=? AND bulan=?",
                [$data['tgt_hari'], $data['offline'], $data['online'],
                 $data['dry'], $data['perishable'], $data['khusus'], $data['virtual'] ?? 0,
                 $kode, $tahun, $bulan]
            );
        } else {
            $this->db->insert(
                "INSERT INTO target_spd (kode_toko, tahun, bulan, tgt_hari, target_offline, target_online, target_dry, target_perishable, target_produk_khusus, target_virtual)
                 VALUES (?,?,?,?,?,?,?,?,?,?)",
                [$kode, $tahun, $bulan, $data['tgt_hari'], $data['offline'], $data['online'],
                 $data['dry'], $data['perishable'], $data['khusus'], $data['virtual'] ?? 0]
            );
        }
        return true;
    }

    // ============================================================
    // NKL
    // ============================================================
    public function getNkl(string $kode, int $tahun, int $bulan): ?array {
        return $this->db->fetch(
            "SELECT * FROM aktual_nkl WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveNkl(string $kode, int $tahun, int $bulan, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM aktual_nkl WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
        if ($existing) {
            $this->db->execute(
                "UPDATE aktual_nkl SET is_audit=?, sales_gross_all=?, aktual_nkl_all=?,
                    sales_gross_buah=?, aktual_nkl_buah=?, catatan=?
                 WHERE kode_toko=? AND tahun=? AND bulan=?",
                [$data['is_audit'], $data['sales_gross_all'], $data['nkl_all'],
                 $data['sales_gross_buah'], $data['nkl_buah'], $data['catatan'] ?? '',
                 $kode, $tahun, $bulan]
            );
        } else {
            $this->db->insert(
                "INSERT INTO aktual_nkl (kode_toko, tahun, bulan, is_audit, sales_gross_all, aktual_nkl_all, sales_gross_buah, aktual_nkl_buah, catatan)
                 VALUES (?,?,?,?,?,?,?,?,?)",
                [$kode, $tahun, $bulan, $data['is_audit'], $data['sales_gross_all'],
                 $data['nkl_all'], $data['sales_gross_buah'], $data['nkl_buah'], $data['catatan'] ?? '']
            );
        }
        return true;
    }

    // ============================================================
    // NBR
    // ============================================================
    public function getNbr(string $kode, int $tahun, int $bulan): ?array {
        return $this->db->fetch(
            "SELECT * FROM aktual_nbr WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveNbr(string $kode, int $tahun, int $bulan, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM aktual_nbr WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
        if ($existing) {
            $this->db->execute(
                "UPDATE aktual_nbr SET sales_nett_dry=?, aktual_nbr_dry=?, modul_main=?, modul_ach=?, catatan=?
                 WHERE kode_toko=? AND tahun=? AND bulan=?",
                [$data['sales_nett_dry'], $data['nbr_dry'], $data['modul_main'],
                 $data['modul_ach'], $data['catatan'] ?? '', $kode, $tahun, $bulan]
            );
        } else {
            $this->db->insert(
                "INSERT INTO aktual_nbr (kode_toko, tahun, bulan, sales_nett_dry, aktual_nbr_dry, modul_main, modul_ach, catatan)
                 VALUES (?,?,?,?,?,?,?,?)",
                [$kode, $tahun, $bulan, $data['sales_nett_dry'], $data['nbr_dry'],
                 $data['modul_main'], $data['modul_ach'], $data['catatan'] ?? '']
            );
        }
        return true;
    }

    // ============================================================
    // STD
    // ============================================================
    public function getStd(string $kode, int $tahun, int $bulan): ?array {
        return $this->db->fetch(
            "SELECT * FROM aktual_std WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveStd(string $kode, int $tahun, int $bulan, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM aktual_std WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
        $fields = [
            'std_poinku_b3','std_poinku_b2','std_poinku_b1','aktual_std_poinku',
            'std_2item_b3','std_2item_b2','std_2item_b1','aktual_std_2item',
            'trx_ipayment_b3','trx_ipayment_b2','trx_ipayment_b1','aktual_trx_ipayment',
            'trx_nontunai_b3','trx_nontunai_b2','trx_nontunai_b1','aktual_trx_nontunai',
            'catatan'
        ];
        if ($existing) {
            $sets = implode(',', array_map(fn($f) => "$f=?", $fields));
            $vals = array_map(fn($f) => $data[$f] ?? 0, $fields);
            $vals[] = $kode; $vals[] = $tahun; $vals[] = $bulan;
            $this->db->execute("UPDATE aktual_std SET $sets WHERE kode_toko=? AND tahun=? AND bulan=?", $vals);
        } else {
            $cols = implode(',', $fields);
            $placeholders = implode(',', array_fill(0, count($fields), '?'));
            $vals = array_map(fn($f) => $data[$f] ?? 0, $fields);
            $this->db->insert(
                "INSERT INTO aktual_std (kode_toko, tahun, bulan, $cols) VALUES (?,?,?,$placeholders)",
                array_merge([$kode, $tahun, $bulan], $vals)
            );
        }
        return true;
    }

    // ============================================================
    // TURNOVER
    // ============================================================
    public function getTurnover(string $kode, int $tahun, int $bulan): ?array {
        return $this->db->fetch(
            "SELECT * FROM aktual_turnover WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveTurnover(string $kode, int $tahun, int $bulan, array $data): bool {
        $existing = $this->db->fetch(
            "SELECT id FROM aktual_turnover WHERE kode_toko=? AND tahun=? AND bulan=?",
            [$kode, $tahun, $bulan]
        );
        if ($existing) {
            $this->db->execute(
                "UPDATE aktual_turnover SET jumlah_karyawan=?, jumlah_keluar=?, catatan=?
                 WHERE kode_toko=? AND tahun=? AND bulan=?",
                [$data['jumlah_karyawan'], $data['jumlah_keluar'], $data['catatan'] ?? '',
                 $kode, $tahun, $bulan]
            );
        } else {
            $this->db->insert(
                "INSERT INTO aktual_turnover (kode_toko, tahun, bulan, jumlah_karyawan, jumlah_keluar, catatan)
                 VALUES (?,?,?,?,?,?)",
                [$kode, $tahun, $bulan, $data['jumlah_karyawan'], $data['jumlah_keluar'], $data['catatan'] ?? '']
            );
        }
        return true;
    }

    // ============================================================
    // FULL KPI CALCULATION
    // ============================================================
    public function hitungKpiLengkap(string $kode, int $tahun, int $bulan): array {
        $toko    = $this->getToko($kode);
        $target  = $this->getTargetSpd($kode, $tahun, $bulan);
        $aktualH = $this->getAktualSpdAggregate($kode, $tahun, $bulan);
        $nkl     = $this->getNkl($kode, $tahun, $bulan);
        $nbr     = $this->getNbr($kode, $tahun, $bulan);
        $std     = $this->getStd($kode, $tahun, $bulan);
        $to      = $this->getTurnover($kode, $tahun, $bulan);

        $hariBerjalan = getJumlahHariBerjalan($tahun, $bulan);
        $hariTotal    = getJumlahHariBulan($tahun, $bulan);
        $tgtHari      = $target['tgt_hari'] ?? $hariTotal;

        // ---- SPD ----
        $aktualOffline  = (float)($aktualH['total_offline'] ?? 0);
        $aktualOnline   = (float)($aktualH['total_online'] ?? 0);
        $aktualKhusus   = (float)($aktualH['total_khusus'] ?? 0);
        $aktualDry      = (float)($aktualH['total_dry'] ?? 0);
        $aktualPerish   = (float)($aktualH['total_perishable'] ?? 0);
        $aktualTotal    = $aktualOffline + $aktualOnline;

        $tgtOffline = (float)($target['target_offline'] ?? 0);
        $tgtOnline  = (float)($target['target_online'] ?? 0);
        $tgtKhusus  = (float)($target['target_produk_khusus'] ?? 0);
        $tgtDry     = (float)($target['target_dry'] ?? 0);
        $tgtPerish  = (float)($target['target_perishable'] ?? 0);
        $tgtTotal   = $tgtOffline + $tgtOnline;

        $tgtPropOffline = hitungTargetProporsional($tgtOffline, $tgtHari, $hariBerjalan);
        $tgtPropOnline  = hitungTargetProporsional($tgtOnline, $tgtHari, $hariBerjalan);
        $tgtPropKhusus  = hitungTargetProporsional($tgtKhusus, $tgtHari, $hariBerjalan);
        $tgtPropTotal   = $tgtPropOffline + $tgtPropOnline;

        $achOffline = hitungAchievement($aktualOffline, $tgtPropOffline);
        $achOnline  = hitungAchievement($aktualOnline, $tgtPropOnline);
        $achKhusus  = hitungAchievement($aktualKhusus, $tgtPropKhusus);
        $achTotal   = hitungAchievement($aktualTotal, $tgtPropTotal);

        $poinOffline = hitungPoinSPD($achOffline, KPI_POINTS['spd_offline']);
        $poinOnline  = hitungPoinSPD($achOnline, KPI_POINTS['spd_online']);
        $poinKhusus  = $tgtKhusus > 0 ? hitungPoinSPD($achKhusus, KPI_POINTS['spd_khusus']) : 0;
        $poinSpdTotal = $poinOffline + $poinOnline + ($tgtKhusus > 0 ? $poinKhusus : 0);
        $poinMaksSPD  = KPI_POINTS['spd_offline'] + KPI_POINTS['spd_online'] + ($tgtKhusus > 0 ? KPI_POINTS['spd_khusus'] : 0);

        // ---- NKL ----
        $isAudit     = (int)($nkl['is_audit'] ?? 0);
        $salesAll    = (float)($nkl['sales_gross_all'] ?? 0);
        $nklAll      = (float)($nkl['aktual_nkl_all'] ?? 0);
        $salesBuah   = (float)($nkl['sales_gross_buah'] ?? 0);
        $nklBuah     = (float)($nkl['aktual_nkl_buah'] ?? 0);

        $budgetNklAll  = $salesAll * NKL_ALL_THRESHOLD;
        $pctNklAll     = $salesAll > 0 ? abs($nklAll) / $salesAll * 100 : 0;
        $poinNklAll    = 0;
        $poinNklBuah   = 0;
        $statusNklAll  = 'Proporsional';

        if ($isAudit) {
            $poinNklAll   = $nklAll >= 0 && $nklAll <= $budgetNklAll ? KPI_POINTS['nkl_all'] : 0;
            $poinNklBuah  = $nklBuah >= 0 ? KPI_POINTS['nkl_buah'] : 0;
            $statusNklAll = $nklAll < 0 ? 'OVER Budget' : ($nklAll <= $budgetNklAll ? 'OK' : 'OVER');
        } else {
            $poinNklAll  = KPI_POINTS['nkl_all'];
            $poinNklBuah = KPI_POINTS['nkl_buah'];
            $statusNklAll = 'Proporsional';
        }

        // ---- NBR ----
        $salesDry  = (float)($nbr['sales_nett_dry'] ?? 0);
        $nbrDry    = (float)($nbr['aktual_nbr_dry'] ?? 0);
        $modulMain = (int)($nbr['modul_main'] ?? 0);
        $modulAch  = (int)($nbr['modul_ach'] ?? 0);

        $pctNbrDry    = $salesDry > 0 ? ($nbrDry / $salesDry) * 100 : 0;
        $poinNbrDry   = hitungPoinNBRDry($pctNbrDry);
        $poinNbrKhusus = hitungPoinNBRKhusus($modulMain, $modulAch);

        // ---- STD ----
        $maxPoinku   = max((float)($std['std_poinku_b3'] ?? 0), (float)($std['std_poinku_b2'] ?? 0), (float)($std['std_poinku_b1'] ?? 0));
        $aktPoinku   = (float)($std['aktual_std_poinku'] ?? 0);
        $max2item    = max((float)($std['std_2item_b3'] ?? 0), (float)($std['std_2item_b2'] ?? 0), (float)($std['std_2item_b1'] ?? 0));
        $akt2item    = (float)($std['aktual_std_2item'] ?? 0);
        $maxIpay     = max((float)($std['trx_ipayment_b3'] ?? 0), (float)($std['trx_ipayment_b2'] ?? 0), (float)($std['trx_ipayment_b1'] ?? 0));
        $aktIpay     = (float)($std['aktual_trx_ipayment'] ?? 0);
        $maxNontunai = max((float)($std['trx_nontunai_b3'] ?? 0), (float)($std['trx_nontunai_b2'] ?? 0), (float)($std['trx_nontunai_b1'] ?? 0));
        $aktNontunai = (float)($std['aktual_trx_nontunai'] ?? 0);

        $poinPoinku   = hitungPoinSTD($aktPoinku, $maxPoinku, KPI_POINTS['std_poinku']);
        $poin2item    = hitungPoinSTD($akt2item, $max2item, KPI_POINTS['std_2item']);
        $poinIpay     = hitungPoinSTD($aktIpay, $maxIpay, KPI_POINTS['trx_ipayment']);
        $poinNontunai = hitungPoinSTD($aktNontunai, $maxNontunai, KPI_POINTS['trx_nontunai']);

        // ---- TURNOVER ----
        $jumlahKeluar = (int)($to['jumlah_keluar'] ?? 0);
        $poinTO = hitungPoinTurnover($jumlahKeluar);

        // ---- TOTALS ----
        $poinNklTotal = $poinNklAll + $poinNklBuah;
        $poinNbrTotal = $poinNbrDry + $poinNbrKhusus;
        $poinStdTotal = $poinPoinku + $poin2item + $poinIpay + $poinNontunai;
        $poinTotal    = $poinSpdTotal + $poinNklTotal + $poinNbrTotal + $poinStdTotal + $poinTO;
        $poinMaks     = $poinMaksSPD + KPI_POINTS['nkl_total'] + KPI_POINTS['nbr_total'] + KPI_POINTS['std_total'] + KPI_POINTS['turnover'];

        return [
            'toko'          => $toko,
            'tahun'         => $tahun,
            'bulan'         => $bulan,
            'hari_berjalan' => $hariBerjalan,
            'hari_total'    => $hariTotal,
            'tgl_hari_ini'  => date('d/m/Y'),
            'spd' => [
                'target_offline'   => $tgtOffline,
                'target_online'    => $tgtOnline,
                'target_khusus'    => $tgtKhusus,
                'target_dry'       => $tgtDry,
                'target_perishable'=> $tgtPerish,
                'target_total'     => $tgtTotal,
                'target_prop'      => $tgtPropTotal,
                'target_prop_off'  => $tgtPropOffline,
                'target_prop_on'   => $tgtPropOnline,
                'target_prop_khusus'=> $tgtPropKhusus,
                'aktual_offline'   => $aktualOffline,
                'aktual_online'    => $aktualOnline,
                'aktual_khusus'    => $aktualKhusus,
                'aktual_dry'       => $aktualDry,
                'aktual_perishable'=> $aktualPerish,
                'aktual_berjalan'  => $aktualTotal,
                'ach_offline'      => $achOffline,
                'ach_online'       => $achOnline,
                'ach_khusus'       => $achKhusus,
                'ach_total'        => $achTotal,
                'poin_offline'     => $poinOffline,
                'poin_online'      => $poinOnline,
                'poin_khusus'      => $poinKhusus,
                'poin'             => $poinSpdTotal,
                'poin_maks'        => $poinMaksSPD,
                'ada_khusus'       => $tgtKhusus > 0,
            ],
            'nkl' => [
                'is_audit'      => $isAudit,
                'sales_all'     => $salesAll,
                'nkl_all'       => $nklAll,
                'budget_all'    => $budgetNklAll,
                'pct_all'       => $pctNklAll,
                'status_all'    => $statusNklAll,
                'sales_buah'    => $salesBuah,
                'nkl_buah'      => $nklBuah,
                'poin_all'      => $poinNklAll,
                'poin_buah'     => $poinNklBuah,
                'poin'          => $poinNklTotal,
            ],
            'nbr' => [
                'sales_dry'    => $salesDry,
                'nbr_dry'      => $nbrDry,
                'pct_dry'      => $pctNbrDry,
                'poin_dry'     => $poinNbrDry,
                'modul_main'   => $modulMain,
                'modul_ach'    => $modulAch,
                'poin_khusus'  => $poinNbrKhusus,
                'poin'         => $poinNbrTotal,
            ],
            'std' => [
                'b3_poinku'    => $std['std_poinku_b3'] ?? 0,
                'b2_poinku'    => $std['std_poinku_b2'] ?? 0,
                'b1_poinku'    => $std['std_poinku_b1'] ?? 0,
                'max_poinku'   => $maxPoinku,
                'akt_poinku'   => $aktPoinku,
                'b3_2item'     => $std['std_2item_b3'] ?? 0,
                'b2_2item'     => $std['std_2item_b2'] ?? 0,
                'b1_2item'     => $std['std_2item_b1'] ?? 0,
                'max_2item'    => $max2item,
                'akt_2item'    => $akt2item,
                'b3_ipay'      => $std['trx_ipayment_b3'] ?? 0,
                'b2_ipay'      => $std['trx_ipayment_b2'] ?? 0,
                'b1_ipay'      => $std['trx_ipayment_b1'] ?? 0,
                'max_ipay'     => $maxIpay,
                'akt_ipay'     => $aktIpay,
                'b3_nontunai'  => $std['trx_nontunai_b3'] ?? 0,
                'b2_nontunai'  => $std['trx_nontunai_b2'] ?? 0,
                'b1_nontunai'  => $std['trx_nontunai_b1'] ?? 0,
                'max_nontunai' => $maxNontunai,
                'akt_nontunai' => $aktNontunai,
                'poin_poinku'  => $poinPoinku,
                'poin_2item'   => $poin2item,
                'poin_ipay'    => $poinIpay,
                'poin_nontunai'=> $poinNontunai,
                'poin'         => $poinStdTotal,
            ],
            'turnover' => [
                'jumlah_karyawan' => $to['jumlah_karyawan'] ?? 0,
                'jumlah_keluar'   => $jumlahKeluar,
                'poin'            => $poinTO,
            ],
            'poin_spd'     => $poinSpdTotal,
            'poin_nkl'     => $poinNklTotal,
            'poin_nbr'     => $poinNbrTotal,
            'poin_std'     => $poinStdTotal,
            'poin_to'      => $poinTO,
            'poin_total'   => $poinTotal,
            'poin_maks'    => $poinMaks,
            'poin_pct'     => $poinMaks > 0 ? round(($poinTotal / $poinMaks) * 100, 2) : 0,
            'grade'        => getGradeInsentif($poinTotal, $poinMaks),
        ];
    }
}

    // ============================================================
    // OTP VALIDATION
    // ============================================================
    public function getOtp(string $kode): ?string {
        $row = $this->db->fetch("SELECT kode_otp FROM toko WHERE kode_toko=?", [$kode]);
        return $row['kode_otp'] ?? null;
    }

    public function validateOtp(string $kode, string $inputOtp): bool {
        $otp = $this->getOtp($kode);
        return $otp && $otp === trim($inputOtp);
    }

    public function updateOtp(string $kode, string $newOtp): bool {
        $this->db->execute("UPDATE toko SET kode_otp=?, otp_updated_at=NOW() WHERE kode_toko=?", [$newOtp, $kode]);
        return true;
    }

    // ============================================================
    // NBR HARIAN (detail per nota)
    // ============================================================
    public function getNbrHarian(string $kode, int $tahun, int $bulan): array {
        return $this->db->fetchAll(
            "SELECT * FROM aktual_nbr_harian WHERE kode_toko=? AND YEAR(tanggal)=? AND MONTH(tanggal)=? ORDER BY tanggal DESC, id DESC",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveNbrHarian(string $kode, array $data): bool {
        $this->db->insert(
            "INSERT INTO aktual_nbr_harian (kode_toko, tanggal, no_nbr, jenis, nama_produk, nilai, catatan) VALUES (?,?,?,?,?,?,?)",
            [$kode, $data['tanggal'], $data['no_nbr'], $data['jenis'], $data['nama_produk'], $data['nilai'], $data['catatan'] ?? '']
        );
        return true;
    }

    public function deleteNbrHarian(int $id): bool {
        $this->db->execute("DELETE FROM aktual_nbr_harian WHERE id=?", [$id]);
        return true;
    }

    public function getNbrHarianAggregate(string $kode, int $tahun, int $bulan): array {
        return $this->db->fetchAll(
            "SELECT tanggal, SUM(CASE WHEN jenis='dry' THEN nilai ELSE 0 END) as total_dry,
                SUM(CASE WHEN jenis IN('khusus','perishable') THEN nilai ELSE 0 END) as total_khusus,
                SUM(nilai) as total_all, COUNT(*) as jumlah_nota
             FROM aktual_nbr_harian WHERE kode_toko=? AND YEAR(tanggal)=? AND MONTH(tanggal)=?
             GROUP BY tanggal ORDER BY tanggal ASC",
            [$kode, $tahun, $bulan]
        );
    }

    // ============================================================
    // STD HARIAN
    // ============================================================
    public function getStdHarian(string $kode, int $tahun, int $bulan): array {
        return $this->db->fetchAll(
            "SELECT * FROM aktual_std_harian WHERE kode_toko=? AND YEAR(tanggal)=? AND MONTH(tanggal)=? ORDER BY tanggal ASC",
            [$kode, $tahun, $bulan]
        );
    }

    public function saveStdHarian(string $kode, string $tanggal, array $data): bool {
        $existing = $this->db->fetch("SELECT id FROM aktual_std_harian WHERE kode_toko=? AND tanggal=?", [$kode, $tanggal]);
        if ($existing) {
            $this->db->execute(
                "UPDATE aktual_std_harian SET poinku_trx=?,poinku_total_trx=?,item2_trx=?,item2_total_trx=?,ipayment_trx=?,nontunai_trx=?,nontunai_total_trx=?,catatan=? WHERE kode_toko=? AND tanggal=?",
                [$data['poinku_trx'],$data['poinku_total_trx'],$data['item2_trx'],$data['item2_total_trx'],
                 $data['ipayment_trx'],$data['nontunai_trx'],$data['nontunai_total_trx'],$data['catatan']??'',$kode,$tanggal]
            );
        } else {
            $this->db->insert(
                "INSERT INTO aktual_std_harian (kode_toko,tanggal,poinku_trx,poinku_total_trx,item2_trx,item2_total_trx,ipayment_trx,nontunai_trx,nontunai_total_trx,catatan) VALUES (?,?,?,?,?,?,?,?,?,?)",
                [$kode,$tanggal,$data['poinku_trx'],$data['poinku_total_trx'],$data['item2_trx'],$data['item2_total_trx'],
                 $data['ipayment_trx'],$data['nontunai_trx'],$data['nontunai_total_trx'],$data['catatan']??'']
            );
        }
        return true;
    }

    public function deleteStdHarian(int $id): bool {
        $this->db->execute("DELETE FROM aktual_std_harian WHERE id=?", [$id]);
        return true;
    }
}
