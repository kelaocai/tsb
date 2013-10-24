//
//  PostListViewController.m
//  ipapp
//
//  Created by kelaocai on 13-10-1.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "PostListViewController.h"
#import "common.h"
#import "AFNetworking.h"
#import "PostListTableCell.h"
//#import "MJRefresh.h"



@interface PostListViewController () 
{
    ///MJRefreshFooterView *_footer;
    //NSMutableArray *_data;

}

@end

@implementation PostListViewController
@synthesize _posts=posts;

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    
    return self;
  
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    _current_page=1;
    _next_page=1;
    _page_size=2;
    
    
    posts=[[NSMutableArray alloc] initWithCapacity:10];


    
    
    //进度条
    self.hud=[[MBProgressHUD alloc] initWithView:self.tableView];
    [self.view addSubview:self.hud];
    [self.view bringSubviewToFront:self.hud];
    self.hud.delegate=self;
    self.hud.labelText=@"数据加载中...";
    

    [self loadMore];
    
    //禁止高亮选择
    //self.tableView.allowsSelection = NO;
    

    
 
    

}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{

    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [posts count]+1;
    
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    

    
    
    
    // 如果是最后一行，则显示加载更多，或是提示已加载完全
    
    //NSLog(@"indexPath:%d,post_count:%d",[indexPath row],[posts count]);
    if([indexPath row] == ([posts count])) {
        
        static NSString *idd=@"cell";
        //NSString *identifier=[NSString stringWithFormat:@"cell%d",indexPath.row];
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:idd];
        if (cell==nil) {
            cell=[[[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:idd] autorelease];
        }else{
            NSLog(@"from deque%d",[indexPath row]);
        }

        
        if (_current_page==_total_page) {
            cell.textLabel.text=@"已到最后一页";
        }else{
            cell.textLabel.text=@"加载更多..";
        }
        
        return cell;
        
        // cell.accessoryType = UITableViewCellAccessoryNone;
    }else {
        
        NSString *idd=[NSString stringWithFormat:@"cell%d",indexPath.row];
         
         PostListTableCell *cell = [tableView dequeueReusableCellWithIdentifier:idd];
        if (nil==cell) {
            cell = [[PostListTableCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:idd];
            NSDictionary *post=[posts objectAtIndex:[indexPath row]];
            NSString *msg=[post objectForKey:@"message"];
            //设置头像
            NSURL *avatar_url = [NSURL URLWithString:[post objectForKey:@"avatar"]];
            [cell.avatar setImageWithURL:avatar_url];
            //设置内容块大小
            CGSize lable_size=[self countMassage:msg sizeForIndex:indexPath];
            [cell.message setFrame:CGRectMake(80, 20, 200.0f, lable_size.height)];
            [cell.messageBgView setFrame:CGRectMake(50, 10, 250.0f, lable_size.height+20.0f)];
            cell.message.text=msg;
            //作者+时间戳
            [cell.author setFrame:CGRectMake(80,lable_size.height+30.0f,200.0f,20.0f)];
            cell.author.text=[NSString stringWithFormat:@"%@    %@",[post objectForKey:@"author"],[post objectForKey:@"date"]];
            //回复按钮
            [cell.reply setFrame:CGRectMake(266,lable_size.height+30.0f,20.0f,20.0f)];
            
        }else{
            NSLog(@"custormCell from deque%d",[indexPath row]);
        }
         
         return cell;

        
    }

    
    
    
    
        
    
    
    // Configure the cell...
    
    
}


- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath{
    if ([indexPath row]==[posts count]) {
        return 40.0f;
    }else{
        
        NSDictionary *post=[posts objectAtIndex:[indexPath row]];
        NSString *msg=[post objectForKey:@"message"];
        return [self countMassage:msg sizeForIndex:indexPath].height+60.0f;
    }


};


//计算message文字区域大小
-(CGSize)countMassage:(NSString *)msg sizeForIndex:(NSIndexPath *)indexPath{
    CGSize size=CGSizeMake(200.0f, MAXFLOAT);
    UIFont *font = [UIFont systemFontOfSize:14.0f];
    CGSize lable_size=[msg sizeWithFont:font constrainedToSize:size lineBreakMode:NSLineBreakByWordWrapping];
    return lable_size;
}








// 加载更多数据，此处可以换成从远程服务器获取最新的_size条数据
-(void)loadMore
{
    
    [_hud show:YES];
    NSMutableArray *more;
    more = [[[NSMutableArray alloc] initWithCapacity:0] autorelease];
    [self.hud show:YES];
    //NSLog(@"current_page:%d",_current_page);
    //读取远程板块数据
    NSString *BaseURLString=BASE_URL;
    NSString *url = [NSString stringWithFormat:@"%@?c=forum&a=forum_post_list&tid=%@&page=%d", BaseURLString,self.tid,_next_page];
    // 把远程数据转成NSDictionary的数据
    NSDictionary *data = [NSDictionary dictionaryWithContentsOfURLString:url];
    if (nil!=data) {
        [self.hud hide:YES];
        NSArray *posts_data=[data objectForKey:@"data"];
        for (int i=0; i<[posts_data count]; i++) {
            [more addObject:[posts_data objectAtIndex:i]];
        }
        
        
        if([posts_data count]>1){
            NSDictionary *mypager=[data objectForKey:@"pager"];
            _next_page=[[mypager objectForKey:@"next_page"] intValue];
            _current_page=[[mypager objectForKey:@"current_page"] intValue];
            _total_page=[[mypager objectForKey:@"total_page"] intValue];
            _page_size=[[mypager objectForKey:@"page_size"] intValue];
        
        }else{
            _next_page=1;
            _current_page=1;
            _total_page=1;
            _page_size=1;
        }

        [self appendTableWith:more];
        
    }

   
    
}

// 添加数据到当前TableView中去
-(void) appendTableWith:(NSMutableArray *)data
{
    // 添加到当前的数据源中
    for (int i=0; i<[data count]; i++) {
        [posts addObject:[data objectAtIndex:i]];
    }
    //[self.tableView reloadData];
    NSMutableArray *insertIndexPaths = [NSMutableArray arrayWithCapacity:[data count]];
    for(int ind =0;ind<[data count];ind++)
    {
        NSIndexPath *newPath = [NSIndexPath indexPathForRow:[posts indexOfObject:[data objectAtIndex:ind]] inSection:0];
        [insertIndexPaths addObject:newPath];
    }
    [self.tableView insertRowsAtIndexPaths:insertIndexPaths withRowAnimation:UITableViewRowAnimationBottom];
    [self.tableView reloadData];
    
    // 选到当前的行数
    int begin = [posts count];//(_current_page-1)*_page_size;
    //NSLog(@"bging:%d",begin);
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:begin inSection:0];
    [self.tableView selectRowAtIndexPath:indexPath  animated:YES scrollPosition:UITableViewScrollPositionTop];
    
}








- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    if([indexPath row] == [posts count])
    {
        
        if (_current_page<_total_page) {
             [self loadMore];
        }
        
        [tableView deselectRowAtIndexPath:indexPath animated:YES];
        //[self remoteGetPostList];

    }
}




- (void)dealloc {
    [_postTableView release];
    [_hud release];
    //[_footer release];
    [super dealloc];
}


- (IBAction)moreButton:(id)sender {
    [self performSelectorInBackground:@selector(loadMore) withObject:nil];
    //[self.tableView deselectRowAtIndexPath:indexPath animated:YES];

    
}
@end
