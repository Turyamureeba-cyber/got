<?php
require_once 'db.php';

// Fetch districts
$districts = $db->query("SELECT * FROM districts ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$page_num = isset($_GET['page_num']) ? intval($_GET['page_num']) : 1;
$limit = 10;
$offset = ($page_num - 1) * $limit;

$stmt = $db->prepare("SELECT * FROM items WHERE status='active' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// AJAX: only return item cards
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    foreach ($items as $item) { ?>
        <div class="item-card">
            <img src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'assets/placeholder.png'; ?>" alt="Item Image">
            <h4><?= htmlspecialchars($item['title']); ?></h4>
            <p><?= htmlspecialchars(substr($item['description'],0,100)); ?>...</p>
            <p><strong>Category:</strong> <?= htmlspecialchars($item['category']); ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($item['location']); ?></p>
            <div class="item-actions">
                <a href="#" class="edit-btn view-item-btn" data-id="<?= $item['id']; ?>">View Details</a>
            </div>
        </div>
    <?php }
    exit;
}
?>
<style>
/* Browse by Location Section */
.locations {
    text-align: center;
    margin: 60px 0;
}

.locations h3 {
    font-size: 2rem;
    margin-bottom: 30px;
    color: #2575fc;
    font-weight: 600;
}

.location-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.location-card {
    background: #f4f7fb;
    color: #333;
    padding: 20px 25px;
    border-radius: 14px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    font-weight: 500;
    font-size: 1.1rem;
    min-width: 150px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

.location-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.2);
    background: #2575fc;
    color: white;
}


.items-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}
.item-card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    background: #fff;
}
.item-card img {
    width: 100%;
    height: auto;
    border-radius: 8px;
}
.item-actions {
    margin-top: 10px;
}
.no-items {
    text-align: center;
    font-style: italic;
    color: #555;
}
/* ...existing code... */

.items-grid {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

/* Item Grid and Cards - Compact Version */
.items-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    padding: 10px;
}

.item-card {
    border: 1px solid #e0e0e0;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}

.item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.item-card img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 8px;
}

/* Smaller text for card content */
.item-card h4 {
    font-size: 0.9rem;
    margin: 5px 0;
    line-height: 1.2;
    color: #2575fc;
    font-weight: 600;
}

.item-card p {
    font-size: 0.75rem;
    margin: 3px 0;
    line-height: 1.2;
    color: #666;
}

.item-card .item-actions {
    margin-top: 8px;
}

.item-card .edit-btn {
    display: inline-block;
    padding: 5px 10px;
    background: #2575fc;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.75rem;
    transition: background 0.2s;
}

.item-card .edit-btn:hover {
    background: #1a5cc5;
}

/* Force 3 columns on mobile */
@media (max-width: 600px) {
    .items-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    
    .item-card {
        padding: 8px 5px;
    }
    
    .item-card img {
        height: 80px;
    }
    
    .item-card h4 {
        font-size: 0.8rem;
    }
    
    .item-card p {
        font-size: 0.7rem;
    }
}

/* For very small screens */
/* Item Grid and Cards - Professional Compact Version */
.items-grid {
    display: grid;
    gap: 15px;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    padding: 15px;
}

.item-card {
    border: 1px solid #e8e8e8;
    padding: 12px;
    border-radius: 10px;
    text-align: left;
    background: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.item-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, #2575fc, #6a11cb);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.item-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.12);
}

.item-card:hover::before {
    opacity: 1;
}

.item-card img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 10px;
    transition: transform 0.3s ease;
}

.item-card:hover img {
    transform: scale(1.03);
}

/* Professional typography with left alignment */
.item-card h4 {
    font-size: 0.95rem;
    margin: 6px 0;
    line-height: 1.3;
    color: #2c3e50;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    letter-spacing: -0.2px;
}

.item-card p {
    font-size: 0.78rem;
    margin: 4px 0;
    line-height: 1.3;
    color: #555;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.item-card .item-meta {
    border-top: 1px solid #f0f0f0;
    margin-top: 8px;
    padding-top: 8px;
}

.item-card .item-actions {
    margin-top: 10px;
    text-align: center;
}

.item-card .edit-btn {
    display: inline-block;
    padding: 6px 12px;
    background: linear-gradient(to right, #2575fc, #6a11cb);
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.78rem;
    transition: all 0.3s ease;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.item-card .edit-btn:hover {
    background: linear-gradient(to right, #1a5cc5, #4d0ca7);
    box-shadow: 0 2px 6px rgba(37, 117, 252, 0.4);
}

/* Force 3 columns on mobile */
@media (max-width: 600px) {
    .items-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    
    .item-card {
        padding: 10px 8px;
    }
    
    .item-card img {
        height: 85px;
    }
    
    .item-card h4 {
        font-size: 0.85rem;
    }
    
    .item-card p {
        font-size: 0.72rem;
    }
    
    .item-card .edit-btn {
        padding: 5px 10px;
        font-size: 0.72rem;
    }
}

/* For very small screens */
@media (max-width: 400px) {
    .items-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .item-card {
        padding: 8px 6px;
    }
    
    .item-card img {
        height: 75px;
    }
}

/* Animation for card entrance */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.item-card {
    animation: fadeInUp 0.5s ease forwards;
}

/* Stagger the animation for a nicer effect */
.item-card:nth-child(1) { animation-delay: 0.1s; }
.item-card:nth-child(2) { animation-delay: 0.2s; }
.item-card:nth-child(3) { animation-delay: 0.3s; }
.item-card:nth-child(4) { animation-delay: 0.4s; }
.item-card:nth-child(5) { animation-delay: 0.5s; }
.item-card:nth-child(6) { animation-delay: 0.6s; }
/* Modal styling */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  top: 0; left: 0; right:0; bottom:0;
  width:100%; height:100%;
  background: rgba(0,0,0,0.6);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background:#fff;
  border-radius:8px;
  padding:20px;
  width:90%;
  max-width:600px;
  max-height:80%;
  overflow-y:auto;
  box-shadow:0 4px 20px rgba(0,0,0,0.3);
}
.close-btn {
  position:absolute;
  top:15px;
  right:20px;
  font-size:24px;
  cursor:pointer;
  color:#333;
}

</style>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h2>Lost Something? Found Something?</h2>
    <p>Connecting Ugandans to reunite with the lost items fast and securely.</p>
    <form action="index.php" method="get">
      <input type="hidden" name="page" value="search">
      <input type="text" name="q" placeholder="Search lost items...">
      <button type="submit">Search</button>
    </form>
  </div>
</section>

<!-- Categories -->
<section class="categories">
  <div class="dropdown mobile-only">
    <button class="dropbtn">Category</button>
    <div class="dropdown-content">
      <a href="#">📱 Phones</a>
      <a href="#">🆔 IDs</a>
      <a href="#">🚗 Vehicle Plates</a>
      <a href="#">💼 Bags</a>
      <a href="#">📄 Documents</a>
      <a href="#">🧑 Loved Ones</a>
    </div>
  </div>
  <div class="dropdown mobile-only">
    <button class="dropbtn">Location</button>
    <div class="dropdown-content">
      <a href="#">🏨 Hotels</a>
      <a href="#">🎉 Events</a>
      <a href="#">🏫 Schools</a>
      <a href="#">🛍️ Shopping Centers</a>
      <a href="#">🚍 Transport Hubs</a>
      <a href="#">✈️ Airport</a>
      <div class="dropdown-district">
        📍 District
        <select class="district-select">
          <option value="">Select District</option>
          <?php foreach($districts as $district): ?>
            <option value="<?= htmlspecialchars($district['id']) ?>">
              <?= htmlspecialchars($district['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>
</section>

<!-- Filter Buttons -->
<div class="filter-menu">
  <button class="filter-btn active" data-type="all">ALL</button>
  <button class="filter-btn" data-type="lost">LOST</button>
  <button class="filter-btn" data-type="found">FOUND</button>
</div>

<!-- Items Grid -->
<div class="items-grid" id="items-grid">
  <?php if(count($items) > 0): ?>
    <?php foreach($items as $item): ?>
      <div class="item-card">
        <img src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'assets/placeholder.png'; ?>" alt="Item Image">
        <h4><?= htmlspecialchars($item['title']); ?></h4>
        <p><?= htmlspecialchars(substr($item['description'],0,100)); ?>...</p>
        <p><strong>Category:</strong> <?= htmlspecialchars($item['category']); ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($item['location']); ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($item['type']); ?></p>
        <div class="item-actions">
          <a href="#" class="edit-btn view-item-btn" data-id="<?= $item['id']; ?>">View Details</a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="no-items">No items found.</p>
  <?php endif; ?>
</div>
<div id="loading" style="text-align:center; display:none;">
  <p>Loading more items...</p>
</div>

<!-- Modal -->
<div id="itemModal" class="modal">
  <div class="modal-content">
    <span class="close-btn">&times;</span>
    <div id="modal-body">Loading...</div>
  </div>
</div>

<script>
let page = 1, loading = false, noMoreItems = false, currentType = "all";

// Infinite scroll
window.addEventListener('scroll', () => {
  if(noMoreItems) return;
  if((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100 && !loading){
    loading = true; page++;
    document.getElementById('loading').style.display='block';
    fetch(`load_items.php?page_num=${page}&type=${currentType}&ajax=1`)
      .then(res=>res.text())
      .then(data=>{
        const grid=document.getElementById('items-grid');
        if(data.trim()!==''){
          grid.insertAdjacentHTML('beforeend', data);
          loading=false;
          document.getElementById('loading').style.display='none';
        }else{
          document.getElementById('loading').innerHTML='<p>No more items</p>';
          noMoreItems=true;
        }
      });
  }
});

// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    page=1; noMoreItems=false;
    document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    currentType=btn.dataset.type;
    document.getElementById('items-grid').innerHTML='<p>Loading items...</p>';
    fetch(`load_items.php?page_num=1&type=${currentType}&ajax=1`)
      .then(res=>res.text())
      .then(data=>{
        document.getElementById('items-grid').innerHTML=data||'<p class="no-items">No items found.</p>';
      })
      .catch(()=>{
        document.getElementById('items-grid').innerHTML='<p class="no-items">Failed to load items.</p>';
      });
  });
});

// Modal for item view
const modal=document.getElementById('itemModal'), modalBody=document.getElementById('modal-body');
document.addEventListener('click',e=>{
  if(e.target.classList.contains('view-item-btn')){
    e.preventDefault();
    modal.style.display='flex';
    modalBody.innerHTML='Loading...';
    const id=e.target.dataset.id;
    fetch(`index.php?page=view_item&id=${id}&ajax=1`)
      .then(res=>res.text())
      .then(data=>modalBody.innerHTML=data)
      .catch(()=>modalBody.innerHTML='<p>Error loading item.</p>');
  }
});
document.querySelector('.close-btn').addEventListener('click',()=>modal.style.display='none');
window.addEventListener('click',e=>{ if(e.target===modal) modal.style.display='none'; });
</script>
