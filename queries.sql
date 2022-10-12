-- Query 1: display all transactions for a batch (filter: merchant_id + batch_date + batch_ref_num)
SELECT t.*
FROM transactions t
INNER JOIN batches b ON b.batch_hash = t.batch_hash
WHERE b.mid = '344858307505959269'
  AND b.batch_ref_num = '713911985564755663442139'
  AND b.batch_date = '2018-05-05'

-- Query 2: display statistics for a batch (filter: merchant_id + batch_date + batch_ref_num)
--          grouped by transaction card type

SELECT DISTINCT
    t.trans_card_type,
    SUM(t.trans_amount) as amount
FROM transactions t
INNER JOIN batches b ON b.batch_hash = t.batch_hash
INNER JOIN merchants m ON m.mid = b.mid
WHERE m.mid = '79524081202206784'
  AND b.batch_ref_num = '865311392860455095554114'
  AND b.batch_date = '2018-05-05'
GROUP BY t.trans_card_type


-- Query 3: display top 10 merchants (by total amount) for a given date range (batch_date)
--          merchant id, merchant name, total amount, number of transactions

SELECT  m.id,
        m.dba,
        COUNT(t.id) as transctions_number,
        SUM(t.trans_amount) as amount
FROM merchants m
INNER JOIN batches b ON b.mid = m.mid
INNER JOIN transactions t ON t.batch_hash = b.batch_hash
WHERE b.batch_date >= '2018-05-01'
  AND b.batch_date <= '2018-05-05'
GROUP BY m.id
ORDER BY amount desc
LIMIT 10
