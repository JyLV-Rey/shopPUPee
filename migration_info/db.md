-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.order (
  order_id integer NOT NULL DEFAULT nextval('"Order_order_id_seq"'::regclass),
  buyer_id integer NOT NULL,
  status USER-DEFINED NOT NULL DEFAULT 'Pending'::order_status,
  ordered_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_deleted boolean NOT NULL DEFAULT false,
  CONSTRAINT order_pkey PRIMARY KEY (order_id),
  CONSTRAINT fk_order_buyer FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id)
);
CREATE TABLE public.product (
  product_id integer NOT NULL DEFAULT nextval('product_product_id_seq'::regclass),
  name character varying NOT NULL,
  description text,
  price numeric NOT NULL,
  is_deleted boolean NOT NULL DEFAULT false,
  seller_id integer,
  created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  category text,
  quantity integer,
  CONSTRAINT product_pkey PRIMARY KEY (product_id),
  CONSTRAINT fk_product_seller FOREIGN KEY (seller_id) REFERENCES public.seller(seller_id)
);
CREATE TABLE public.seller (
  seller_id integer NOT NULL DEFAULT nextval('seller_seller_id_seq'::regclass),
  buyer_id integer NOT NULL,
  applied_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_deleted boolean NOT NULL DEFAULT false,
  seller_name text NOT NULL,
  address_id integer,
  application_id integer UNIQUE,
  CONSTRAINT seller_pkey PRIMARY KEY (seller_id),
  CONSTRAINT fk_seller_buyer FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id),
  CONSTRAINT seller_address_id_fkey FOREIGN KEY (address_id) REFERENCES public.address(address_id),
  CONSTRAINT seller_application_id_fkey FOREIGN KEY (application_id) REFERENCES public.seller_application(application_id)
);
CREATE TABLE public.address (
  address_id integer NOT NULL DEFAULT nextval('address_address_id_seq'::regclass),
  buyer_id integer NOT NULL,
  street text NOT NULL,
  city text NOT NULL,
  postal_code text,
  province text NOT NULL,
  barangay text NOT NULL,
  region text NOT NULL,
  unit_floor text,
  additional_notes text,
  CONSTRAINT address_pkey PRIMARY KEY (address_id),
  CONSTRAINT address_buyer_id_fkey FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id)
);
CREATE TABLE public.buyer (
  buyer_id integer NOT NULL DEFAULT nextval('buyer_buyer_id_seq'::regclass),
  first_name text NOT NULL,
  email text NOT NULL UNIQUE,
  password text NOT NULL,
  phone text,
  created_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_deleted boolean NOT NULL DEFAULT false,
  last_name text NOT NULL,
  CONSTRAINT buyer_pkey PRIMARY KEY (buyer_id)
);
CREATE TABLE public.cancel (
  cancel_id integer NOT NULL DEFAULT nextval('cancel_cancel_id_seq'::regclass),
  order_id integer NOT NULL UNIQUE,
  cancel_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  cancel_reason text,
  CONSTRAINT cancel_pkey PRIMARY KEY (cancel_id),
  CONSTRAINT fk_cancel_order FOREIGN KEY (order_id) REFERENCES public.order(order_id)
);
CREATE TABLE public.cartitem (
  cart_item_id integer NOT NULL DEFAULT nextval('cartitem_cart_item_id_seq'::regclass),
  buyer_id integer NOT NULL,
  product_id integer NOT NULL,
  quantity integer NOT NULL CHECK (quantity > 0),
  CONSTRAINT cartitem_pkey PRIMARY KEY (cart_item_id),
  CONSTRAINT cartitem_buyer_id_fkey FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id),
  CONSTRAINT cartitem_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.product(product_id)
);
CREATE TABLE public.delivery (
  delivery_id integer NOT NULL DEFAULT nextval('delivery_delivery_id_seq'::regclass),
  order_id integer NOT NULL,
  delivery_status USER-DEFINED NOT NULL DEFAULT 'Preparing'::delivery_status,
  courier_service text,
  created_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tracking_number bigint NOT NULL DEFAULT nextval('delivery_tracking_number_seq'::regclass),
  delivery_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  buyer_address_id integer,
  CONSTRAINT delivery_pkey PRIMARY KEY (delivery_id),
  CONSTRAINT fk_delivery_address FOREIGN KEY (buyer_address_id) REFERENCES public.address(address_id),
  CONSTRAINT fk_delivery_order FOREIGN KEY (order_id) REFERENCES public.order(order_id)
);
CREATE TABLE public.order_item (
  order_item_id integer NOT NULL DEFAULT nextval('orderitem_order_item_id_seq'::regclass),
  order_id integer NOT NULL,
  product_id integer NOT NULL,
  quantity integer NOT NULL CHECK (quantity > 0),
  CONSTRAINT order_item_pkey PRIMARY KEY (order_item_id),
  CONSTRAINT fk_orderitem_order FOREIGN KEY (order_id) REFERENCES public.order(order_id),
  CONSTRAINT fk_orderitem_product FOREIGN KEY (product_id) REFERENCES public.product(product_id)
);
CREATE TABLE public.payment (
  payment_id integer NOT NULL DEFAULT nextval('payment_payment_id_seq'::regclass),
  order_id integer NOT NULL,
  payment_method USER-DEFINED NOT NULL,
  payment_status USER-DEFINED NOT NULL,
  paid_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT payment_pkey PRIMARY KEY (payment_id),
  CONSTRAINT fk_payment_order FOREIGN KEY (order_id) REFERENCES public.order(order_id)
);
CREATE TABLE public.price_history (
  history_id integer NOT NULL DEFAULT nextval('price_history_history_id_seq'::regclass),
  product_id integer NOT NULL,
  price numeric NOT NULL,
  date_set timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT price_history_pkey PRIMARY KEY (history_id),
  CONSTRAINT fk_product FOREIGN KEY (product_id) REFERENCES public.product(product_id)
);
CREATE TABLE public.product_image (
  image_id integer NOT NULL DEFAULT nextval('productimage_image_id_seq'::regclass),
  product_id integer NOT NULL,
  image_url text NOT NULL,
  CONSTRAINT product_image_pkey PRIMARY KEY (image_id),
  CONSTRAINT fk_productimage_product FOREIGN KEY (product_id) REFERENCES public.product(product_id)
);
CREATE TABLE public.refund (
  refund_id integer NOT NULL DEFAULT nextval('refund_refund_id_seq'::regclass),
  refund_status USER-DEFINED NOT NULL DEFAULT 'Pending'::refund_status,
  processed_at timestamp without time zone DEFAULT now(),
  refund_reason text,
  order_id integer NOT NULL UNIQUE,
  CONSTRAINT refund_pkey PRIMARY KEY (refund_id),
  CONSTRAINT fk_order_refund FOREIGN KEY (order_id) REFERENCES public.order(order_id)
);
CREATE TABLE public.review (
  review_id integer NOT NULL DEFAULT nextval('review_review_id_seq'::regclass),
  product_id integer NOT NULL,
  buyer_id integer NOT NULL,
  rating integer CHECK (rating >= 1 AND rating <= 5),
  comment text,
  created_at timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT review_pkey PRIMARY KEY (review_id),
  CONSTRAINT fk_review_buyer FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id),
  CONSTRAINT fk_review_product FOREIGN KEY (product_id) REFERENCES public.product(product_id)
);
CREATE TABLE public.seller_application (
  application_id integer NOT NULL DEFAULT nextval('seller_application_application_id_seq'::regclass),
  seller_name text NOT NULL,
  application_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  buyer_id integer,
  valid_id_url text NOT NULL,
  status USER-DEFINED NOT NULL DEFAULT 'Pending'::seller_status,
  address_id integer NOT NULL,
  CONSTRAINT seller_application_pkey PRIMARY KEY (application_id),
  CONSTRAINT fk_application_buyer FOREIGN KEY (buyer_id) REFERENCES public.buyer(buyer_id),
  CONSTRAINT seller_application_address_id_fkey FOREIGN KEY (address_id) REFERENCES public.address(address_id)
);