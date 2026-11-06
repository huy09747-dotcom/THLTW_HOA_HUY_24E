export function categoryPath(item, categories) {
  const names = [item.name];
  const visited = new Set([Number(item.id)]);
  let parentId = Number(item.parent_id);
  while (parentId && !visited.has(parentId)) {
    visited.add(parentId);
    const parent = categories.find(entry => Number(entry.id) === parentId);
    if (!parent) {
      names.unshift(`Danh mục #${parentId} (không khả dụng)`);
      break;
    }
    names.unshift(parent.name);
    parentId = Number(parent.parent_id);
  }
  return names.join(" → ");
}

export function categoryOptions(categories) {
  return categories.map(item => ({
    ...item,
    name: categoryPath(item, categories) + (Number(item.status) === 0 ? " (đang ẩn)" : ""),
  })).sort((a, b) => a.name.localeCompare(b.name, "vi"));
}
