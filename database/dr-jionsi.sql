--
-- PostgreSQL database dump
--

\restrict 52ZIA9xOaVQTLvMMgYTOgPvaUoz6e7KZEIJ77NNVaB6oAetUD6ojVrZOyevmHMI

-- Dumped from database version 15.19
-- Dumped by pg_dump version 15.19

-- Started on 2026-11-02 21:11:08

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 49780)
-- Name: btree_gist; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS btree_gist WITH SCHEMA public;


--
-- TOC entry 3770 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION btree_gist; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION btree_gist IS 'support for indexing common datatypes in GiST';


--
-- TOC entry 3 (class 3079 OID 50575)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 3771 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 217 (class 1259 OID 50431)
-- Name: admin_users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.admin_users (
    id integer NOT NULL,
    nama character varying(100) NOT NULL,
    nomor_whatsapp character varying(20) NOT NULL,
    password character varying(255) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    username character varying(50) NOT NULL
);


ALTER TABLE public.admin_users OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 50430)
-- Name: admin_users_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.admin_users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.admin_users_id_seq OWNER TO postgres;

--
-- TOC entry 3772 (class 0 OID 0)
-- Dependencies: 216
-- Name: admin_users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.admin_users_id_seq OWNED BY public.admin_users.id;


--
-- TOC entry 231 (class 1259 OID 50616)
-- Name: harga_paket_jam; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.harga_paket_jam (
    durasi_jam integer NOT NULL,
    harga numeric(12,2) NOT NULL,
    CONSTRAINT harga_paket_jam_durasi_jam_check CHECK ((durasi_jam = ANY (ARRAY[3, 6, 12]))),
    CONSTRAINT harga_paket_jam_harga_check CHECK ((harga >= (0)::numeric))
);


ALTER TABLE public.harga_paket_jam OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 50469)
-- Name: kamar; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kamar (
    id integer NOT NULL,
    nomor_kamar character varying(10) NOT NULL,
    tipe_kamar character varying(50) NOT NULL,
    lantai smallint DEFAULT 1 NOT NULL,
    kapasitas integer NOT NULL,
    harga_per_malam numeric(12,2) NOT NULL,
    status character varying(20) DEFAULT 'Tersedia'::character varying,
    deskripsi text,
    foto_url character varying(255),
    kondisi_diperbarui_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT kamar_harga_per_malam_check CHECK ((harga_per_malam >= (0)::numeric)),
    CONSTRAINT kamar_kapasitas_check CHECK ((kapasitas > 0)),
    CONSTRAINT kamar_status_check CHECK (((status)::text = ANY ((ARRAY['Tersedia'::character varying, 'Terisi'::character varying, 'Perbaikan'::character varying])::text[])))
);


ALTER TABLE public.kamar OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 50468)
-- Name: kamar_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kamar_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.kamar_id_seq OWNER TO postgres;

--
-- TOC entry 3773 (class 0 OID 0)
-- Dependencies: 221
-- Name: kamar_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kamar_id_seq OWNED BY public.kamar.id;


--
-- TOC entry 230 (class 1259 OID 50558)
-- Name: log_status_reservasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.log_status_reservasi (
    id bigint NOT NULL,
    reservasi_id integer NOT NULL,
    status_lama character varying(30),
    status_baru character varying(30) NOT NULL,
    diubah_oleh integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.log_status_reservasi OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 50557)
-- Name: log_status_reservasi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.log_status_reservasi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.log_status_reservasi_id_seq OWNER TO postgres;

--
-- TOC entry 3774 (class 0 OID 0)
-- Dependencies: 229
-- Name: log_status_reservasi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.log_status_reservasi_id_seq OWNED BY public.log_status_reservasi.id;


--
-- TOC entry 232 (class 1259 OID 50623)
-- Name: login_percobaan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.login_percobaan (
    ip character varying(45) NOT NULL,
    gagal integer DEFAULT 0 NOT NULL,
    terkunci_sampai timestamp without time zone,
    terakhir timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.login_percobaan OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 50515)
-- Name: pembayaran; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pembayaran (
    id integer NOT NULL,
    reservasi_id integer NOT NULL,
    qris_id integer,
    nominal numeric(12,2) NOT NULL,
    tanggal_pembayaran date DEFAULT CURRENT_DATE NOT NULL,
    bukti_file character varying(255) NOT NULL,
    status_verifikasi character varying(20) DEFAULT 'Menunggu'::character varying NOT NULL,
    diverifikasi_oleh integer,
    diverifikasi_at timestamp without time zone,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pembayaran_status_verifikasi_check CHECK (((status_verifikasi)::text = ANY ((ARRAY['Menunggu'::character varying, 'Terverifikasi'::character varying, 'Ditolak'::character varying])::text[])))
);


ALTER TABLE public.pembayaran OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 50514)
-- Name: pembayaran_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.pembayaran_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.pembayaran_id_seq OWNER TO postgres;

--
-- TOC entry 3775 (class 0 OID 0)
-- Dependencies: 225
-- Name: pembayaran_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.pembayaran_id_seq OWNED BY public.pembayaran.id;


--
-- TOC entry 218 (class 1259 OID 50440)
-- Name: pengaturan_villa; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pengaturan_villa (
    id smallint DEFAULT 1 NOT NULL,
    nama_properti character varying(100) NOT NULL,
    kode_properti character varying(10) DEFAULT 'DRJ'::character varying NOT NULL,
    whatsapp_utama character varying(20) NOT NULL,
    email_operasional character varying(100),
    alamat_lengkap text,
    batas_waktu_keep_menit integer DEFAULT 15 NOT NULL,
    batas_refund_hari_sebelum integer DEFAULT 1 NOT NULL,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pengaturan_villa_id_check CHECK ((id = 1))
);


ALTER TABLE public.pengaturan_villa OWNER TO postgres;

--
-- TOC entry 220 (class 1259 OID 50454)
-- Name: qris_statis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.qris_statis (
    id integer NOT NULL,
    gambar character varying(255) NOT NULL,
    aktif boolean DEFAULT false NOT NULL,
    diunggah_oleh integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.qris_statis OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 50453)
-- Name: qris_statis_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.qris_statis_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.qris_statis_id_seq OWNER TO postgres;

--
-- TOC entry 3776 (class 0 OID 0)
-- Dependencies: 219
-- Name: qris_statis_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.qris_statis_id_seq OWNED BY public.qris_statis.id;


--
-- TOC entry 228 (class 1259 OID 50541)
-- Name: refund; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.refund (
    id integer NOT NULL,
    reservasi_id integer NOT NULL,
    alasan text,
    status character varying(20) DEFAULT 'Diajukan'::character varying NOT NULL,
    catatan_admin text,
    diajukan_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    diputuskan_at timestamp without time zone,
    CONSTRAINT refund_status_check CHECK (((status)::text = ANY ((ARRAY['Diajukan'::character varying, 'Disetujui'::character varying, 'Ditolak'::character varying, 'Selesai'::character varying])::text[])))
);


ALTER TABLE public.refund OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 50540)
-- Name: refund_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.refund_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.refund_id_seq OWNER TO postgres;

--
-- TOC entry 3777 (class 0 OID 0)
-- Dependencies: 227
-- Name: refund_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.refund_id_seq OWNED BY public.refund.id;


--
-- TOC entry 224 (class 1259 OID 50487)
-- Name: reservasi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reservasi (
    id integer NOT NULL,
    kode_booking character varying(50) NOT NULL,
    kamar_id integer,
    nama_pemesan character varying(100) NOT NULL,
    whatsapp_pemesan character varying(20) NOT NULL,
    whatsapp_4_digit character(4) GENERATED ALWAYS AS ("right"((whatsapp_pemesan)::text, 4)) STORED,
    jumlah_tamu integer NOT NULL,
    catatan text,
    waktu_checkin timestamp without time zone NOT NULL,
    durasi_jam integer NOT NULL,
    waktu_checkout timestamp without time zone NOT NULL,
    total_tagihan numeric(12,2) NOT NULL,
    status_booking character varying(30) DEFAULT 'Di-Keep'::character varying,
    batas_waktu_keep timestamp without time zone NOT NULL,
    setuju_kebijakan boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT reservasi_check CHECK ((waktu_checkout > waktu_checkin)),
    CONSTRAINT reservasi_durasi_jam_check CHECK ((durasi_jam > 0)),
    CONSTRAINT reservasi_jumlah_tamu_check CHECK ((jumlah_tamu > 0)),
    CONSTRAINT reservasi_status_booking_check CHECK (((status_booking)::text = ANY ((ARRAY['Di-Keep'::character varying, 'Menunggu Konfirmasi'::character varying, 'Dibayar'::character varying, 'Kedaluwarsa'::character varying, 'Dibatalkan'::character varying, 'Refund Diajukan'::character varying])::text[])))
);


ALTER TABLE public.reservasi OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 50486)
-- Name: reservasi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.reservasi_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.reservasi_id_seq OWNER TO postgres;

--
-- TOC entry 3778 (class 0 OID 0)
-- Dependencies: 223
-- Name: reservasi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.reservasi_id_seq OWNED BY public.reservasi.id;


--
-- TOC entry 3523 (class 2604 OID 50434)
-- Name: admin_users id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users ALTER COLUMN id SET DEFAULT nextval('public.admin_users_id_seq'::regclass);


--
-- TOC entry 3533 (class 2604 OID 50472)
-- Name: kamar id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kamar ALTER COLUMN id SET DEFAULT nextval('public.kamar_id_seq'::regclass);


--
-- TOC entry 3550 (class 2604 OID 50561)
-- Name: log_status_reservasi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_status_reservasi ALTER COLUMN id SET DEFAULT nextval('public.log_status_reservasi_id_seq'::regclass);


--
-- TOC entry 3543 (class 2604 OID 50518)
-- Name: pembayaran id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembayaran ALTER COLUMN id SET DEFAULT nextval('public.pembayaran_id_seq'::regclass);


--
-- TOC entry 3530 (class 2604 OID 50457)
-- Name: qris_statis id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.qris_statis ALTER COLUMN id SET DEFAULT nextval('public.qris_statis_id_seq'::regclass);


--
-- TOC entry 3547 (class 2604 OID 50544)
-- Name: refund id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.refund ALTER COLUMN id SET DEFAULT nextval('public.refund_id_seq'::regclass);


--
-- TOC entry 3538 (class 2604 OID 50490)
-- Name: reservasi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservasi ALTER COLUMN id SET DEFAULT nextval('public.reservasi_id_seq'::regclass);


--
-- TOC entry 3749 (class 0 OID 50431)
-- Dependencies: 217
-- Data for Name: admin_users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.admin_users (id, nama, nomor_whatsapp, password, created_at, username) FROM stdin;
1	Portal Pengelola	081233449081	$2a$10$5VRkHGUhNuyyxqRzP56rjuyDNQRZ9CiB0dKZHRS7zhIvo.2pyMHcW	2026-10-31 16:14:24.420103	admin_drjionsi_
\.


--
-- TOC entry 3763 (class 0 OID 50616)
-- Dependencies: 231
-- Data for Name: harga_paket_jam; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.harga_paket_jam (durasi_jam, harga) FROM stdin;
3	80000.00
6	100000.00
12	125000.00
\.


--
-- TOC entry 3754 (class 0 OID 50469)
-- Dependencies: 222
-- Data for Name: kamar; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kamar (id, nomor_kamar, tipe_kamar, lantai, kapasitas, harga_per_malam, status, deskripsi, foto_url, kondisi_diperbarui_at, created_at) FROM stdin;
1	201	Family Suite Panderman	2	6	250000.00	Tersedia	Kamar keluarga yang luas di lantai 2, nyaman untuk hingga 6 tamu. Dilengkapi Wi-Fi, Smart TV, dan air panas.	/uploads/kamar/201.jpg	2026-10-28 16:14:24.420103	2026-10-31 16:14:24.420103
2	101	Deluxe Arjuno	1	2	250000.00	Tersedia	Kamar nyaman untuk 2 tamu di lantai 1 dengan air panas, Smart TV, dan kamar mandi dalam.	/uploads/kamar/101.jpg	2026-10-26 16:14:24.420103	2026-10-31 16:14:24.420103
3	102	Twin Welirang	1	3	250000.00	Tersedia	Kamar twin untuk hingga 3 tamu dengan air panas, Smart TV, dan akses dapur bersama.	/uploads/kamar/102.jpg	2026-10-29 16:14:24.420103	2026-10-31 16:14:24.420103
4	202	Family Suite Semeru	2	6	250000.00	Tersedia	Kamar keluarga di lantai 2 dengan pemandangan pegunungan, muat hingga 6 tamu. Dilengkapi Wi-Fi, Smart TV, dan air panas.	/uploads/kamar/202.jpg	2026-10-30 16:14:24.420103	2026-10-31 16:14:24.420103
5	103	Deluxe Kawi	1	2	250000.00	Tersedia	Kamar hangat untuk 2 tamu dengan air panas, Smart TV, dan kamar mandi dalam, cocok untuk pasangan.	/uploads/kamar/103.jpg	2026-10-24 16:14:24.420103	2026-10-31 16:14:24.420103
6	104	Twin Butak	1	3	250000.00	Tersedia	Kamar twin untuk hingga 3 tamu dengan air panas, Smart TV, dan akses dapur bersama.	/uploads/kamar/104.jpg	2026-10-27 16:14:24.420103	2026-10-31 16:14:24.420103
\.


--
-- TOC entry 3762 (class 0 OID 50558)
-- Dependencies: 230
-- Data for Name: log_status_reservasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.log_status_reservasi (id, reservasi_id, status_lama, status_baru, diubah_oleh, created_at) FROM stdin;
\.


--
-- TOC entry 3764 (class 0 OID 50623)
-- Dependencies: 232
-- Data for Name: login_percobaan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.login_percobaan (ip, gagal, terkunci_sampai, terakhir) FROM stdin;
\.


--
-- TOC entry 3758 (class 0 OID 50515)
-- Dependencies: 226
-- Data for Name: pembayaran; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pembayaran (id, reservasi_id, qris_id, nominal, tanggal_pembayaran, bukti_file, status_verifikasi, diverifikasi_oleh, diverifikasi_at, created_at) FROM stdin;
\.


--
-- TOC entry 3750 (class 0 OID 50440)
-- Dependencies: 218
-- Data for Name: pengaturan_villa; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pengaturan_villa (id, nama_properti, kode_properti, whatsapp_utama, email_operasional, alamat_lengkap, batas_waktu_keep_menit, batas_refund_hari_sebelum, updated_at) FROM stdin;
1	Villa D-R Jionii	DRJ	+62 812-3344-9081	info@drjionsi.com	Batu Highland Retreat, Jl. Songgoriti No. 18, Batu	15	1	2026-11-02 11:45:31.247117
\.


--
-- TOC entry 3752 (class 0 OID 50454)
-- Dependencies: 220
-- Data for Name: qris_statis; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.qris_statis (id, gambar, aktif, diunggah_oleh, created_at) FROM stdin;
1	/uploads/qris/qris-lama.png	f	1	2026-09-01 16:14:24.420103
2	/assets/img/Qris.jpeg	t	1	2026-10-21 16:14:24.420103
\.


--
-- TOC entry 3760 (class 0 OID 50541)
-- Dependencies: 228
-- Data for Name: refund; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.refund (id, reservasi_id, alasan, status, catatan_admin, diajukan_at, diputuskan_at) FROM stdin;
\.


--
-- TOC entry 3756 (class 0 OID 50487)
-- Dependencies: 224
-- Data for Name: reservasi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.reservasi (id, kode_booking, kamar_id, nama_pemesan, whatsapp_pemesan, jumlah_tamu, catatan, waktu_checkin, durasi_jam, waktu_checkout, total_tagihan, status_booking, batas_waktu_keep, setuju_kebijakan, created_at) FROM stdin;
\.


--
-- TOC entry 3779 (class 0 OID 0)
-- Dependencies: 216
-- Name: admin_users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.admin_users_id_seq', 2, true);


--
-- TOC entry 3780 (class 0 OID 0)
-- Dependencies: 221
-- Name: kamar_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kamar_id_seq', 6, true);


--
-- TOC entry 3781 (class 0 OID 0)
-- Dependencies: 229
-- Name: log_status_reservasi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.log_status_reservasi_id_seq', 1, false);


--
-- TOC entry 3782 (class 0 OID 0)
-- Dependencies: 225
-- Name: pembayaran_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.pembayaran_id_seq', 1, false);


--
-- TOC entry 3783 (class 0 OID 0)
-- Dependencies: 219
-- Name: qris_statis_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.qris_statis_id_seq', 2, true);


--
-- TOC entry 3784 (class 0 OID 0)
-- Dependencies: 227
-- Name: refund_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.refund_id_seq', 1, false);


--
-- TOC entry 3785 (class 0 OID 0)
-- Dependencies: 223
-- Name: reservasi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.reservasi_id_seq', 1, false);


--
-- TOC entry 3567 (class 2606 OID 50439)
-- Name: admin_users admin_users_nomor_whatsapp_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_nomor_whatsapp_key UNIQUE (nomor_whatsapp);


--
-- TOC entry 3569 (class 2606 OID 50437)
-- Name: admin_users admin_users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.admin_users
    ADD CONSTRAINT admin_users_pkey PRIMARY KEY (id);


--
-- TOC entry 3595 (class 2606 OID 50622)
-- Name: harga_paket_jam harga_paket_jam_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.harga_paket_jam
    ADD CONSTRAINT harga_paket_jam_pkey PRIMARY KEY (durasi_jam);


--
-- TOC entry 3577 (class 2606 OID 50485)
-- Name: kamar kamar_nomor_kamar_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kamar
    ADD CONSTRAINT kamar_nomor_kamar_key UNIQUE (nomor_kamar);


--
-- TOC entry 3579 (class 2606 OID 50483)
-- Name: kamar kamar_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kamar
    ADD CONSTRAINT kamar_pkey PRIMARY KEY (id);


--
-- TOC entry 3593 (class 2606 OID 50564)
-- Name: log_status_reservasi log_status_reservasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_status_reservasi
    ADD CONSTRAINT log_status_reservasi_pkey PRIMARY KEY (id);


--
-- TOC entry 3597 (class 2606 OID 50629)
-- Name: login_percobaan login_percobaan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.login_percobaan
    ADD CONSTRAINT login_percobaan_pkey PRIMARY KEY (ip);


--
-- TOC entry 3583 (class 2606 OID 50506)
-- Name: reservasi no_double_booking; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservasi
    ADD CONSTRAINT no_double_booking EXCLUDE USING gist (kamar_id WITH =, tsrange(waktu_checkin, waktu_checkout) WITH &&) WHERE (((status_booking)::text = ANY ((ARRAY['Di-Keep'::character varying, 'Menunggu Konfirmasi'::character varying, 'Dibayar'::character varying])::text[])));


--
-- TOC entry 3589 (class 2606 OID 50524)
-- Name: pembayaran pembayaran_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembayaran
    ADD CONSTRAINT pembayaran_pkey PRIMARY KEY (id);


--
-- TOC entry 3572 (class 2606 OID 50452)
-- Name: pengaturan_villa pengaturan_villa_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pengaturan_villa
    ADD CONSTRAINT pengaturan_villa_pkey PRIMARY KEY (id);


--
-- TOC entry 3575 (class 2606 OID 50461)
-- Name: qris_statis qris_statis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.qris_statis
    ADD CONSTRAINT qris_statis_pkey PRIMARY KEY (id);


--
-- TOC entry 3591 (class 2606 OID 50551)
-- Name: refund refund_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.refund
    ADD CONSTRAINT refund_pkey PRIMARY KEY (id);


--
-- TOC entry 3585 (class 2606 OID 50504)
-- Name: reservasi reservasi_kode_booking_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservasi
    ADD CONSTRAINT reservasi_kode_booking_key UNIQUE (kode_booking);


--
-- TOC entry 3587 (class 2606 OID 50502)
-- Name: reservasi reservasi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservasi
    ADD CONSTRAINT reservasi_pkey PRIMARY KEY (id);


--
-- TOC entry 3570 (class 1259 OID 50630)
-- Name: admin_users_username_key; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX admin_users_username_key ON public.admin_users USING btree (username);


--
-- TOC entry 3573 (class 1259 OID 50467)
-- Name: hanya_satu_qris_aktif; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX hanya_satu_qris_aktif ON public.qris_statis USING btree (aktif) WHERE aktif;


--
-- TOC entry 3580 (class 1259 OID 50513)
-- Name: idx_reservasi_cek; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reservasi_cek ON public.reservasi USING btree (kode_booking, whatsapp_4_digit);


--
-- TOC entry 3581 (class 1259 OID 50512)
-- Name: idx_reservasi_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_reservasi_status ON public.reservasi USING btree (status_booking, batas_waktu_keep);


--
-- TOC entry 3604 (class 2606 OID 50570)
-- Name: log_status_reservasi log_status_reservasi_diubah_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_status_reservasi
    ADD CONSTRAINT log_status_reservasi_diubah_oleh_fkey FOREIGN KEY (diubah_oleh) REFERENCES public.admin_users(id);


--
-- TOC entry 3605 (class 2606 OID 50565)
-- Name: log_status_reservasi log_status_reservasi_reservasi_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.log_status_reservasi
    ADD CONSTRAINT log_status_reservasi_reservasi_id_fkey FOREIGN KEY (reservasi_id) REFERENCES public.reservasi(id) ON DELETE CASCADE;


--
-- TOC entry 3600 (class 2606 OID 50535)
-- Name: pembayaran pembayaran_diverifikasi_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembayaran
    ADD CONSTRAINT pembayaran_diverifikasi_oleh_fkey FOREIGN KEY (diverifikasi_oleh) REFERENCES public.admin_users(id);


--
-- TOC entry 3601 (class 2606 OID 50530)
-- Name: pembayaran pembayaran_qris_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembayaran
    ADD CONSTRAINT pembayaran_qris_id_fkey FOREIGN KEY (qris_id) REFERENCES public.qris_statis(id);


--
-- TOC entry 3602 (class 2606 OID 50525)
-- Name: pembayaran pembayaran_reservasi_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pembayaran
    ADD CONSTRAINT pembayaran_reservasi_id_fkey FOREIGN KEY (reservasi_id) REFERENCES public.reservasi(id) ON DELETE CASCADE;


--
-- TOC entry 3598 (class 2606 OID 50462)
-- Name: qris_statis qris_statis_diunggah_oleh_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.qris_statis
    ADD CONSTRAINT qris_statis_diunggah_oleh_fkey FOREIGN KEY (diunggah_oleh) REFERENCES public.admin_users(id);


--
-- TOC entry 3603 (class 2606 OID 50552)
-- Name: refund refund_reservasi_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.refund
    ADD CONSTRAINT refund_reservasi_id_fkey FOREIGN KEY (reservasi_id) REFERENCES public.reservasi(id) ON DELETE CASCADE;


--
-- TOC entry 3599 (class 2606 OID 50507)
-- Name: reservasi reservasi_kamar_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reservasi
    ADD CONSTRAINT reservasi_kamar_id_fkey FOREIGN KEY (kamar_id) REFERENCES public.kamar(id) ON DELETE CASCADE;


-- Completed on 2026-11-02 21:11:09

--
-- PostgreSQL database dump complete
--

\unrestrict 52ZIA9xOaVQTLvMMgYTOgPvaUoz6e7KZEIJ77NNVaB6oAetUD6ojVrZOyevmHMI