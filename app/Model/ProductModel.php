<?php
class ProductModel
{
    /** @var PDO */
    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRecommendationsForUser(int $userId): array
    {
        $sql = "
            SELECT 
                p.PRODUCT_ID,
                p.PRODUCT_NAME,
                p.DESCRIPTION,
                p.PRICE,
                p.IMAGE_FILE,
                bs.BODY_TYPE,
                s.SEASON_TYPE,
                c.COLOR_VALUE,
                i.SIZE,
                i.QUANTITY
            FROM users u
            JOIN products p
                ON p.BODY_SHAPE_ID = u.BODY_SHAPE_ID
            JOIN inventory i
                ON i.PRODUCT_ID = p.PRODUCT_ID
            JOIN colors c
                ON c.COLOR_ID = i.COLOR_ID
            JOIN seasons s
                ON c.SEASON_ID = s.SEASON_ID
            LEFT JOIN body_shapes bs
                ON bs.BODY_SHAPE_ID = u.BODY_SHAPE_ID
            WHERE 
                u.USER_ID        = :user_id
                AND u.SEASON_ID IS NOT NULL
                AND u.BODY_SHAPE_ID IS NOT NULL
                AND c.SEASON_ID   = u.SEASON_ID
                AND i.QUANTITY    > 0
            ORDER BY 
                p.CREATED_AT DESC,
                p.PRODUCT_NAME
            LIMIT 8
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}