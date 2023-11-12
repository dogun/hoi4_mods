1. 游戏打开-debug
2. 在游戏中找到要独立的省份（prov）编号，例如：9609
3. 在主目录map/strategicregions 中搜索这个编号，找到对应的文件，类似 19-Northern France.txt
4. 复制文件到模组文件夹后，将文件中的省份ID删除
5. 在模组strategicregions目录中新建一个文件，编号参照主目录中最大的文件编号，顺序加1，例如 280-TEST.txt，内容如下：
		strategic_region={
			id=280
			name="STRATEGICREGION_280"
			provinces={
				9609
			}
		}
6. 在模组history/states目录中，新建文件，编号为主目录states的最大值顺序加一，编号顺序很重要，否则会报错，如935-TEST.txt，内如如下：
	state={
		id=935
		name="STATE_935"
		manpower = 100
		state_category = town
		history={
			owner = USA
			add_core_of = FRA
			buildings = {
				infrastructure = 4
				industrial_complex = 2
				air_base = 2
			}
		}
		provinces={
			9609
		}
		local_supplies=0.0 
	}
7. 在主目录history/states中搜索省份编号，找到对应的states文件，例如27-Champagne2.txt
8. 复制文件到模组目录，删除文件中对应的省份编号
9. 复制主目录map下面的airports.txt和rocketsites.txt到模组目录
10. 分别添加新的states编号和对应省份ID到两个文件中，例如：935={9609}
11. 在本地化文件中加入翻译： STATE_935: "美国飞地"